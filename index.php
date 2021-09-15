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
		.item{
			float: left;
			margin-right: 22px;
			margin-bottom: 20px;
			border: 1px solid darkgrey;
			border-radius: 5px;
			width: 200px;
			height: 230px;
			background-color: grey;
		}
		.item img{
			width: 180px;
			height: 180px;
			margin: 10px;
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
	x.onload = x.onerror = function() {
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

function load(){
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
	    	done ++;
	    	if(done >= apiCount)
	    		swal.close();
	        var response;
	        try {
                response = JSON.parse(result);
            } catch (t) {
                return;
            }

            console.log(response);
            addContent(response, link);
	    });
	});
}

function addContent(response, link){
	if(response.image){
		var name;
		if(response.name)
 			name = response.name;
		else
			name = link.split('/').pop();
		$("#contents").append(`<div class="item">\
			<img src="${response.image}">\
			<div class="text-center">${name}</div>\
		</div>`);
	} else {
		console.log('empty image: ', link);
	}
}

</script>


</body>

</html>