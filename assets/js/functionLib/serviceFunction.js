var serviceFunction = {

    /**
     * Змінює поле input = number на input = text
     * @param elements
     */
    checkForInputTypeNumberBug: function (elements) {
        var dummy = document.createElement('input');
        try {
            dummy.type = 'number';
        } catch (ex) {
            //Older IE versions will fail to set the type
        }
        if (typeof(dummy.setSelectionRange) != 'undefined' && typeof(dummy.createTextRange) == 'undefined') {
            //Chrome, Firefox, Safari, Opera only!
            try {
                var sel = dummy.setSelectionRange(0, 0);
            } catch (ex) {
                //This exception is currently thrown in Chrome v33.0.1750.146 as they have removed support
                //for this method on number fields. Thus we need to revert all number fields to text fields.
                elements.each(function () {
                    this.type = 'text';
                });
            }
        }
    }

}

module.exports = serviceFunction;