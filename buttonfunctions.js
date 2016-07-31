function showMore() {
	var list = document.getElementsByClassName("hiddenvideo");
	var i;
	for (i = 0; i < list.length; i++) {
		list[i].style.display = "initial";
	}
}