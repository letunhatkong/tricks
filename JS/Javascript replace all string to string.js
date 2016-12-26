// Replaces all instances of the given substring.
String.prototype.replaceAll = function (strTarget, strSubString) {
    var strText = this;
    var intIndexOfMatch = strText.indexOf(strTarget);
    while (intIndexOfMatch != -1) {
        strText = strText.replace(strTarget, strSubString);
        intIndexOfMatch = strText.indexOf(strTarget);
    }
    return ( strText );
};

// Test
var str;
str = 'I+love+you';
str = str.replaceAll("+", " ");

