(function($, undefined) {

	$.nette.ext({
		success: function (payload, status, jqXHR, settings) {
			if (!settings.nette) {
				return;
			}
			if (settings.nette.el.hasClass('remove-after-click')) {
				if (payload.success === true) {
					$('div[data-employee-uuid="' + payload.removedUuid + '"]').addClass('removed');
					setTimeout(function() {
						$('div[data-employee-uuid="' + payload.removedUuid + '"]').remove();
					}, 500);
				} 
			}
		}
	});

	$.nette.ext({
		success: function (payload, status, jqXHR, settings) {
			if (!settings.nette) {
				return;
			}
			if (payload.addUuid !== 'undefined') {
				$('div[data-employee-uuid="' + payload.addUuid + '"] input[name="name"]').focus();
			}
		}
	});

})(jQuery);

if ($('#chart').length) {
	Chart.defaults.plugins.legend.display = false;
	const ctx = document.getElementById('chart').getContext('2d');
	const chart = new Chart(ctx, {
		type: 'bar',
		data: {
			labels: $('#chart').data('employees'),
			datasets: [{
				label: 'Vek',
				data: $('#chart').data('age'),
				backgroundColor: $('#chart').data('color'),
				borderColor: $('#chart').data('color'),
				borderWidth: 1
			}]
		},
		options: {
			indexAxis: 'y',
			scales: {
				x: {
					beginAtZero: true
				}
			}
		}
	});
}

$(function () {
	$.nette.init();
});
