/* Повертає error = true  і текст помилки, якщо об'єкт карти не знайдено, або шар із заданим ім'я не знайдено
   В іншому випадку повертає шар карти із заданим ім'ям.
*/

module.exports = function (name, myMap) {
    try {
        var layer;
        if (myMap instanceof Map) {
            myMap.getLayers().forEach(function (l) {
                if (l.get('name') === name) {
                    layer = l;
                }
            });
            if (layer === undefined) {
                throw new Error('Шар ' + name + ' не знайдено!');
            }
            return layer;
        } else {
            throw new Error('Об\'єкт карти не знайдено');
        }
    }catch (e) {
        return {
            error: true,
            message: e.message
        };
    }
}