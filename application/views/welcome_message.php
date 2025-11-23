<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Welcome to CodeIgniter</title>

	<style type="text/css">

	::selection { background-color: #E13300; color: white; }
	::-moz-selection { background-color: #E13300; color: white; }

	body {
		background-color: #fff;
		margin: 40px;
		font: 13px/20px normal Helvetica, Arial, sans-serif;
		color: #4F5155;
	}

	a {
		color: #003399;
		background-color: transparent;
		font-weight: normal;
		text-decoration: none;
	}

	a:hover {
		color: #97310e;
	}

	h1 {
		color: #444;
		background-color: transparent;
		border-bottom: 1px solid #D0D0D0;
		font-size: 19px;
		font-weight: normal;
		margin: 0 0 14px 0;
		padding: 14px 15px 10px 15px;
	}

	code {
		font-family: Consolas, Monaco, Courier New, Courier, monospace;
		font-size: 12px;
		background-color: #f9f9f9;
		border: 1px solid #D0D0D0;
		color: #002166;
		display: block;
		margin: 14px 0 14px 0;
		padding: 12px 10px 12px 10px;
	}

	#body {
		margin: 0 15px 0 15px;
		min-height: 96px;
	}

	p {
		margin: 0 0 10px;
		padding:0;
	}

	p.footer {
		text-align: right;
		font-size: 11px;
		border-top: 1px solid #D0D0D0;
		line-height: 32px;
		padding: 0 10px 0 10px;
		margin: 20px 0 0 0;
	}

	#container {
		margin: 10px;
		border: 1px solid #D0D0D0;
		box-shadow: 0 0 8px #D0D0D0;
	}

	/* Modal Styles */
	.modal {
		display: none;
		position: fixed;
		z-index: 1;
		left: 0;
		top: 0;
		width: 100%;
		height: 100%;
		overflow: auto;
		background-color: rgba(0,0,0,0.4);
	}

	.modal-content {
		background-color: #fefefe;
		margin: 15% auto;
		padding: 20px;
		border: 1px solid #888;
		width: 80%;
		max-width: 600px;
		text-align: center;
	}

	.modal-content img {
		max-width: 100%;
		max-height: 400px;
	}

	.modal-content a {
		display: inline-block;
		margin-top: 15px;
		padding: 10px 20px;
		background-color: #003399;
		color: #fff;
		text-decoration: none;
		border-radius: 5px;
	}

	.modal-content a:hover {
		background-color: #97310e;
	}

	.close {
		color: #aaa;
		float: right;
		font-size: 28px;
		font-weight: bold;
	}

	.close:hover,
	.close:focus {
		color: black;
		text-decoration: none;
		cursor: pointer;
	}
	</style>
</head>
<body class="bg-gray-900 text-gray-100">

<!-- Advertisement Modal -->
<?php if (isset($active_ad) && !empty($active_ad)): ?>
<div id="adModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <a href="<?php echo $active_ad['link_url']; ?>" target="_blank">
      <img src="<?php echo base_url($active_ad['image_url']); ?>" alt="<?php echo htmlspecialchars($active_ad['title']); ?>">
    </a>
  </div>
</div>
<?php endif; ?>

<div id="container" class="max-w-2xl mx-auto my-10 p-6 bg-gray-800 rounded-lg shadow-lg ring-1 ring-gray-700">
	<h1 class="text-2xl font-bold text-slate-100 mb-4">Welcome to CodeIgniter!</h1>

	<div id="body" class="space-y-4">
		<p class="text-gray-300">The page you are looking at is being generated dynamically by CodeIgniter.</p>

		<p class="text-gray-300">If you would like to edit this page you'll find it located at:</p>
		<code class="block bg-gray-700 text-gray-200 p-3 rounded text-sm">application/views/welcome_message.php</code>

		<p class="text-gray-300">The corresponding controller for this page is found at:</p>
		<code class="block bg-gray-700 text-gray-200 p-3 rounded text-sm">application/controllers/Welcome.php</code>

		<p class="text-gray-300">If you are exploring CodeIgniter for the very first time, you should start by reading the <a href="userguide3/" class="text-blue-400 hover:underline">User Guide</a>.</p>
	</div>

	<p class="footer mt-6 text-sm text-gray-400">Page rendered in <strong class="text-gray-200">{elapsed_time}</strong> seconds. <?php echo  (ENVIRONMENT === 'development') ?  'CodeIgniter Version <strong class="text-gray-200">' . CI_VERSION . '</strong>' : '' ?></p>
</div>

<script>
// Get the modal
var modal = document.getElementById("adModal");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the page loads, open the modal
window.onload = function() {
    if (modal) {
        modal.style.display = "block";
    }
}

// When the user clicks on <span> (x), close the modal
if (span) {
    span.onclick = function() {
        modal.style.display = "none";
    }
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
</script>

</body>
</html>
