$(document).ready(function() {

	// sidr
	// 要素があれば有効に、無ければボタンを削除
	if ($("div#sidr")[0]) {
		$("#sidr-button").sidr({});
	}
	else {
		$("#sidr-button").remove();
	}
});
