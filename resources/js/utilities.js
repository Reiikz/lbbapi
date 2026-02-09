

async function copyInnerText(element) {
    var range = document.createRange();
    range.selectNode(element);
    window.getSelection().removeAllRanges();
    window.getSelection().addRange(range);

    // console.log(element.innerHTML);

    try {
        await navigator.clipboard.writeText(element.innerHTML);
    } catch (error) {
        console.error(error.message);
    }
}