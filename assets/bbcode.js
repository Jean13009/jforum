function countInstances(open, closed) {
    var opening = document.hop.content_form.value.split(open);
    var closing = document.hop.content_form.value.split(closed);
    return opening.length + closing.length - 2;
}

BBcode = function BBcode(text1, text2) {
    if (text2) {
        var ta = document.getElementById('new_topic_content');
        var firstPos = ta.selectionStart;
        var secondPos = ta.selectionEnd + text1.length;
        ta.value = ta.value.slice(0, firstPos) + text1 + ta.value.slice(firstPos);
        ta.value = ta.value.slice(0, secondPos) + text2 + ta.value.slice(secondPos);
        ta.focus();
    }
    else {
        var ta = document.getElementById('new_topic_content');
        var secondPos = ta.selectionEnd;
        ta.value = ta.value.slice(0, secondPos) + ' ' + text1 + ' ' + ta.value.slice(secondPos);
        ta.focus();
    }
}