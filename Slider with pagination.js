//Read more: http://luis-almeida.github.io/jPages/
//Demo: http://luis-almeida.github.io/jPages/mosaic.html

$(function () {
    $("div.holder").jPages({
        containerID: "itemContainer",
        previous: "←",
        next: "→",
        perPage: 20,
        midRange: 3,
        direction: "random",
        animation: "flipInY"
    });
});
