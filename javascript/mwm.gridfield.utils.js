(function($) {
	$.entwine("ss", function($) {
		$(".gridfield-help-button-dialog").entwine({
			loadDialog: function(deferred) {
				var dialog = this.addClass("loading").children(".ui-dialog-content").empty();

				deferred.done(function(data) {
					dialog.html(data).parent().removeClass("loading");
				});
			}
		});

		$(".ss-gridfield .gridfield-help-button").entwine({
			onclick: function() {
				var dialog = $("<div></div>").appendTo("body").dialog({
					modal: false,
					resizable: true,
					width: 600,
					height: 600,
					close: function() {
						$(this).dialog("destroy").remove();
					}
				});

				dialog.parent().addClass("gridfield-help-button-dialog").loadDialog(
					$.get(this.prop("href"))
				);

				dialog.data("grid", this.closest(".ss-gridfield"));

				return false;
			}
		});
	});
})(jQuery);
