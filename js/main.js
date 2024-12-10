function validateForm(){
    /* const pax = $('#pax').val(); */
    const dateTimeInput  = $('#dateTimeInput').val();
    const warningMessage = $('#warningMessage');


    if(!dateTimeInput){
        warningMessage.show();
        return false;
    }
    warningMessage.hide();
    return true;


}