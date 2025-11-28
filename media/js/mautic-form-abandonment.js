/**
 * Mautic Form Abandonment Tracking
 * Detects when a user starts filling a form but abandons it without submitting
 */
(function(window) {
    'use strict';

    var FormAbandonment = {
        config: {
            inactivityTimeout: 30000, // 30 seconds of inactivity before marking as abandoned
            abandonmentCheckInterval: 5000, // Check every 5 seconds
            enableLogging: false
        },

        sessionData: {},
        inactivityTimers: {},
        checkIntervals: {},

        /**
         * Initialize form abandonment tracking for all forms on page
         */
        init: function() {
            var self = this;

            // Find all form elements
            var forms = document.querySelectorAll('form[data-form-id]');

            forms.forEach(function(form) {
                self.trackForm(form);
            });

            // Also support Mautic forms in iframes
            window.addEventListener('load', function() {
                self.trackIframeForms();
            });
        },

        /**
         * Track a specific form for abandonment
         */
        trackForm: function(form) {
            var self = this;
            var formId = form.getAttribute('data-form-id');
            var trackingId = form.getAttribute('data-tracking-id') || self.generateTrackingId();

            if (!formId) {
                return;
            }

            self.sessionData[trackingId] = {
                formId: formId,
                trackingId: trackingId,
                startTime: Date.now(),
                filledFields: [],
                lastActivityTime: Date.now(),
                hasInteracted: false,
                formElement: form
            };

            self.log('Initialized tracking for form ' + formId);

            // Track form field interactions
            form.addEventListener('focus', function(e) {
                self.onFormFieldFocus(e, trackingId);
            }, true);

            form.addEventListener('change', function(e) {
                self.onFormFieldChange(e, trackingId);
            }, true);

            form.addEventListener('input', function(e) {
                self.onFormFieldInput(e, trackingId);
            }, true);

            // Reset inactivity timer on any interaction
            form.addEventListener('change', function() {
                self.resetInactivityTimer(trackingId);
            });

            form.addEventListener('input', function() {
                self.resetInactivityTimer(trackingId);
            });

            // Stop tracking on form submission
            form.addEventListener('submit', function() {
                self.clearTracking(trackingId);
            });

            // Start inactivity timer
            self.resetInactivityTimer(trackingId);

            // Set up periodic check
            self.checkIntervals[trackingId] = setInterval(function() {
                self.checkAbandonmentStatus(trackingId);
            }, self.config.abandonmentCheckInterval);

            // Track page unload
            window.addEventListener('beforeunload', function() {
                self.onPageUnload(trackingId);
            });
        },

        /**
         * Handle form field focus
         */
        onFormFieldFocus: function(e, trackingId) {
            var self = this;
            var target = e.target;

            if (!target.name || !self.sessionData[trackingId]) {
                return;
            }

            self.sessionData[trackingId].hasInteracted = true;
            self.log('Field focused: ' + target.name);
        },

        /**
         * Handle form field change
         */
        onFormFieldChange: function(e, trackingId) {
            var self = this;
            var target = e.target;

            if (!target.name || !self.sessionData[trackingId]) {
                return;
            }

            var data = self.sessionData[trackingId];
            var fieldName = target.name;

            if (data.filledFields.indexOf(fieldName) === -1) {
                data.filledFields.push(fieldName);
            }

            self.log('Field changed: ' + fieldName);
        },

        /**
         * Handle form field input
         */
        onFormFieldInput: function(e, trackingId) {
            var self = this;
            var target = e.target;

            if (!target.name || !self.sessionData[trackingId]) {
                return;
            }

            self.sessionData[trackingId].lastActivityTime = Date.now();
        },

        /**
         * Reset inactivity timer
         */
        resetInactivityTimer: function(trackingId) {
            var self = this;

            if (self.inactivityTimers[trackingId]) {
                clearTimeout(self.inactivityTimers[trackingId]);
            }

            self.inactivityTimers[trackingId] = setTimeout(function() {
                self.log('Inactivity timeout reached for ' + trackingId);
            }, self.config.inactivityTimeout);
        },

        /**
         * Check if form appears abandoned
         */
        checkAbandonmentStatus: function(trackingId) {
            var self = this;
            var data = self.sessionData[trackingId];

            if (!data || !data.hasInteracted) {
                return;
            }

            var timeSinceActivity = Date.now() - data.lastActivityTime;

            // If user has been inactive for the timeout period
            if (timeSinceActivity >= self.config.inactivityTimeout) {
                self.log('Form appears abandoned: ' + trackingId);
                self.sendAbandonmentEvent(trackingId);
            }
        },

        /**
         * Handle page unload
         */
        onPageUnload: function(trackingId) {
            var self = this;
            var data = self.sessionData[trackingId];

            if (!data || !data.hasInteracted) {
                return;
            }

            // Check if form was submitted
            var form = data.formElement;
            if (form && document.activeElement === form) {
                // Likely submitting, let the submit event handle it
                return;
            }

            self.log('Page unload detected, form abandoned: ' + trackingId);
            self.sendAbandonmentEvent(trackingId);
        },

        /**
         * Send abandonment event to server
         */
        sendAbandonmentEvent: function(trackingId) {
            var self = this;
            var data = self.sessionData[trackingId];

            if (!data) {
                return;
            }

            var abandonmentData = {
                trackingId: trackingId,
                formId: data.formId,
                filledFields: data.filledFields,
                abandonmentPercentage: self.calculateAbandonmentPercentage(data),
                timeSpent: Math.round((Date.now() - data.startTime) / 1000),
                timestamp: new Date().toISOString()
            };

            // Send via beacon if available (better reliability for page unload)
            if (navigator.sendBeacon) {
                var endpoint = MauticDomain + '/form/' + data.formId + '/abandon';
                navigator.sendBeacon(endpoint, JSON.stringify(abandonmentData));
            } else {
                // Fallback to fetch
                var endpoint = MauticDomain + '/form/' + data.formId + '/abandon';
                fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(abandonmentData),
                    keepalive: true // Important for unload scenarios
                }).catch(function() {
                    // Silently fail - don't disrupt user experience
                    self.log('Failed to send abandonment event');
                });
            }

            self.log('Sent abandonment event for form ' + data.formId);
        },

        /**
         * Calculate what percentage of the form was filled
         */
        calculateAbandonmentPercentage: function(data) {
            var form = data.formElement;
            if (!form) {
                return 0;
            }

            // Get all visible form fields
            var allFields = form.querySelectorAll('input[name], select[name], textarea[name]');
            var visibleFields = Array.from(allFields).filter(function(field) {
                return field.offsetParent !== null; // Check if visible
            });

            if (visibleFields.length === 0) {
                return 0;
            }

            var percentage = Math.round((data.filledFields.length / visibleFields.length) * 100);
            return Math.min(percentage, 100);
        },

        /**
         * Clear tracking for a form
         */
        clearTracking: function(trackingId) {
            var self = this;

            if (self.inactivityTimers[trackingId]) {
                clearTimeout(self.inactivityTimers[trackingId]);
                delete self.inactivityTimers[trackingId];
            }

            if (self.checkIntervals[trackingId]) {
                clearInterval(self.checkIntervals[trackingId]);
                delete self.checkIntervals[trackingId];
            }

            delete self.sessionData[trackingId];
            self.log('Cleared tracking for ' + trackingId);
        },

        /**
         * Track forms in iframes (Mautic embedded forms)
         */
        trackIframeForms: function() {
            var self = this;

            // This would need to be handled via postMessage if forms are in cross-origin iframes
            // For now, we track forms in the main document and same-origin iframes
        },

        /**
         * Generate a unique tracking ID
         */
        generateTrackingId: function() {
            return 'track_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        },

        /**
         * Log debug messages
         */
        log: function(message) {
            if (this.config.enableLogging && window.console) {
                console.log('[FormAbandonment] ' + message);
            }
        }
    };

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            FormAbandonment.init();
        });
    } else {
        FormAbandonment.init();
    }

    // Expose to window for external access
    window.MauticFormAbandonment = FormAbandonment;

})(window);
