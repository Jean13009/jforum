
const el = document.getElementsByClassName("postid");
//console.log(el);

for (var i = 0; i < el.length; i++) {
    el[i].addEventListener('click', showComment, false);
}

function showComment(e) {
    console.log(e.path[1].name);
    document.cookie = 'quotes' + e.path[1].name + '=' + e.path[1].name + ';expires=Thu, 18 Dec 2050 12:00:00 UTC; path=/';
}