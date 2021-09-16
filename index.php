<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<title>NFT IFPS Image Viewer</title>

	<!-- Bootstrap Core CSS -->
	<link href="./css/bootstrap.css" rel="stylesheet">
	<link href="./css/sweetalert2.min.css" rel="stylesheet">

	<style>
		.item {
			float: left;
			margin-right: 22px;
			margin-bottom: 20px;
			border: 1px solid darkgrey;
			border-radius: 5px;
			width: 200px;
			height: 230px;
			background-color: grey;
			position: relative;
		}

		.item img {
			width: 180px;
			height: 180px;
			margin: 10px;
		}

		.item:hover img {
			opacity: 0.5;
		}

		.item:hover .refresh {
			display: block;
		}

		.refresh {
			display: none;
			position: absolute;
			top: 80px;
			left: 60px;
		}

		.loader {
			border: 16px solid #f3f3f3;
			border-radius: 50%;
			border-top: 16px solid #3498db;
			width: 120px;
			height: 120px;
			-webkit-animation: spin 2s linear infinite;
			/* Safari */
			animation: spin 2s linear infinite;
			position: absolute;
			top: 50px;
			left: 37px;
		}

		.hide_reload_text {
			display: none;
		}

		.text_center {
			position: absolute;
			bottom: 10px;
			left: 50px
		}
		/* Safari */
		@-webkit-keyframes spin {
			0% {
				-webkit-transform: rotate(0deg);
			}

			100% {
				-webkit-transform: rotate(360deg);
			}
		}

		@keyframes spin {
			0% {
				transform: rotate(0deg);
			}

			100% {
				transform: rotate(360deg);
			}
		}
	</style>

</head>

<body>

	<div class="container">
		<div class="col-12 mb-2 mt-5">
			<h4>Paste your links to here.</h4>
			<textarea style="width: 100%; height: 300px;" id="linkarea"></textarea>
		</div>

		<div class="col-12 text-center mb-2">
			<button class="btn btn-primary" onclick="load()">Load Links</button>
		</div>

		<div id="contents">
		</div>
	</div>

	<script src="./js/jquery-3.3.1.min.js"></script>
	<script src="./js/sweetalert2.min.js"></script>

	<script>

		var cors_api_url = 'https://ifps-cors-anywhere.herokuapp.com/';

		function doCORSRequest(options, printResult) {
			var x = new XMLHttpRequest();
			x.open(options.method, cors_api_url + options.url);
			x.onload = x.onerror = function () {
				printResult(
					// options.method + ' ' + options.url + '\n' +
					// x.status + ' ' + x.statusText + '\n\n' +
					(x.responseText || '')
				);
			};
			if (/^POST/i.test(options.method)) {
				x.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			}
			x.send(options.data);
		}

		var apiCount = 0;
		var done = 0;

		function load() {
			swal.fire({ title: "Please wait...", showConfirmButton: false });
			swal.showLoading();

			$("#contents").html('');

			var linksdata = $("#linkarea").val();
			var links = linksdata.split('\n').filter(e => e != '');
			apiCount = links.length;
			sentCount = 0;

			links.forEach(link => {
				doCORSRequest({
					method: 'GET',
					url: link,
					data: {}
				}, function printResult(result) {
					done++;
					if (done >= apiCount)
						swal.close();
					var response;
					try {
						response = JSON.parse(result);
					} catch (t) {
						console.log('link', link);
						$("#contents").append(`<div class="item">\
					<div class="loader"></div>\
					<div class="refresh">\
						<button class="btn btn-info" onclick="resendApi('${link}')")>Refresh</button>\
					</div>
				</div>`);
						return;
					}

					console.log("response", response);
					addContent(response, link);
				});
			});
		}

		function resendApi(link, event) {
			doCORSRequest({
				method: 'GET',
				url: link,
				data: {}
			}, function printResult(result) {
				done++;
				if (done >= apiCount)
					swal.close();
				var response;
				try {
					response = JSON.parse(result);
					console.log("image & text", response.image, response.name)
					$(event.target).parents('.item').find("img").empty();
					$(event.target).parents('.item').find("img").attr("src", response.image);
					$(event.target).parents('.item').find(".text-center").empty();
					$(event.target).parents('.item').find(".text-center").html(response.name);

				} catch (t) {
					return;
				}
			});
		}

		function addContent(response, link) {
			if (response.image) {
				var name;
				if (response.name)
					name = response.name;
				else
					name = link.split('/').pop();
				if (response.image == '') {
					$("#contents").append(`<div class="item">\
						<div class="loader"></div>
					<div class="text_center">${name}</div>\
					<div class="refresh">\
						<button class="btn btn-info" onclick="resendApi('${link}', event)")>Refresh</button>\
					</div>\
					</div>`);
				} else {
					$("#contents").append(`<div class="item">\
					<img src="${response.image}">\
					<div class="text-center">${name}</div>\
					<div class="refresh">\
						<button class="btn btn-info" onclick="resendApi('${link}', event)")>Refresh</button>\
					</div>\
					</div>`);
				}
			} else {

				console.log('empty image: ', link);
			}
		}

	</script>


</body>

</html>