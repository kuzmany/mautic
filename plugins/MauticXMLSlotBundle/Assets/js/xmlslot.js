  /**
   * Launch builder
   *
   * @param formName
   * @param actionName
   */

   Mautic.mySlotListener = function() {

    Mautic.builderContents.on('slot:init', function(event, slot) {

      slot = mQuery(slot);
      var type = slot.attr('data-slot');


      // Store the slot to a global var
      Mautic.builderSlots.push({slot: slot, type: type});
    });


    Mautic.builderContents.on('slot:change', function(event, params) {

      // Change some slot styles when the values are changed in the slot edit form
      var fieldParam = params.field.attr('data-slot-param');
      var type = params.type;
      console.log(params.field.val());
      Mautic.clearSlotFormError(fieldParam);
      if (fieldParam === 'padding-top' || fieldParam === 'padding-bottom') {
        params.slot.css(fieldParam, params.field.val() + 'px');
      } else if (fieldParam === 'slotname') {
        params.slot.find('.xmlslot').text('{xmlslot='+params.field.val()+'}');
      } else if (fieldParam === 'link-text') {
        params.slot.find('a.xmlslot-button').text(params.field.val());
      } else if (fieldParam === 'hideimages') {
        if(params.field.val()){
          params.slot.find('.xmlslot-image').show();
        }else{
          params.slot.find('.xmlslot-image').hide();
        }
      } else if (fieldParam === 'hidebuttons') {
        if(params.field.val()){
          params.slot.find('.xmlslot-button').show();
        }else{
          params.slot.find('.xmlslot-button').hide();
        }
      } else if (fieldParam === 'customcss') {
          params.slot.find('.xmlslot').css(params.field.val());
      } else if (fieldParam === 'xmllimit') {
          params.slot.find('.xmlslot .xmlslottr').hide();
      }
    });

  };
