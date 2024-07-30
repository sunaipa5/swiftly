function formatPrice(price, type) {
    var formatted;
    if (type == "tr" || type == "TR") {
        formatted = price.toLocaleString('tr-TR', { minimumFractionDigits: 2 });
    } else if (type == "uni") {
        const formattedValue = price.replace('.', '').replace(',', '.');;
        formatted = parseFloat(formattedValue);
    } else if (type == "con") {
        formatted = parseFloat(price.toFixed(2));
    }
    return formatted;
}

function formatNumber(input) {
    input.value = input.value.replace(/[^\d.,]/g, '');
}

function formatInt(input) {
    input.value = input.value.replace(/\D/g, '');
}