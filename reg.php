		function ajaxPost(params)
		{
			var request = new XMLHttpRequest();

			request.onreadystatechange = function()
			{
				if(request.readyState == 4 && request.status == 200)
				{
					document.querySelector('#result').innerHTML = request.responseText;
				}
			}

			request.open('POST', 'ip.php', true);
			request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

			request.send(params);
		}