module.exports = function (jqXHR) {
    try {
        if (jqXHR.responseJSON) {
            if(jqXHR.responseJSON.status) {
                bootboxMessage(jqXHR.responseJSON.message, jqXHR.responseJSON.status);
            }else{
                bootboxMessage(jqXHR.responseJSON.message);
            }
        } else {
            bootboxMessage(jqXHR.responseText);
        }
    } catch (e) {
        alert(e.message);
    }
};