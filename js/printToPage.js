		var items_old = ["Vinyl Record","XBOX One Game","Blu-Ray Box Set","Bagel Maker","Lego Playset"];
		
		var turntable = {item_name:"Audio Technica Fully Automatic Turntable",item_url:"http://www.amazon.com/dp/B008872SIO/ref=sr_ph?ie=UTF8&qid=1405919464&sr=1&keywords=turntable",item_pic:"http://ecx.images-amazon.com/images/I/41DOK%2BdZbNL._SY355_.jpg"};
		var motorcycle = {item_name:"Jetson Electric Bike",item_url:"http://www.amazon.com/gp/product/B00C7SZPQC/ref=s9_simh_gw_p468_d0_i1?pf_rd_m=ATVPDKIKX0DER&pf_rd_s=center-2&pf_rd_r=0RSKQS79A2ECVFWNB9XR&pf_rd_t=101&pf_rd_p=1688200382&pf_rd_i=507846",item_pic:"http://ecx.images-amazon.com/images/I/613eeXUHvBL._SL1500_.jpg"};
		var lego = {item_name:"NBA Lego Playset",item_url:"http://www.amazon.com/Lego-Builders-Kit-Player-Figures/dp/B000WUC4BQ/ref=sr_1_1?s=sporting-goods&ie=UTF8&qid=1405920193&sr=1-1&keywords=lego+playset",item_pic:"http://ecx.images-amazon.com/images/I/51xzvl4WhNL.jpg"};
		var album = {item_name:"The New Tycho Album",item_url:"http://www.amazon.com/Awake-Tycho/dp/B00HWKJGU6/ref=sr_1_cc_1?s=aps&ie=UTF8&qid=1405920254&sr=1-1-catcorr&keywords=tycho+vinyl",item_pic:"http://ecx.images-amazon.com/images/I/81vM9je7RhL._SL1500_.jpg"};
		var xbox = {item_name:"XBOX One",item_url:"http://www.amazon.com/Xbox-One/dp/B00KAI3KW2/ref=sr_1_2?ie=UTF8&qid=1405920318&sr=8-2&keywords=xbox+one",item_pic:"http://ecx.images-amazon.com/images/I/51M32RUTtPL._SL1000_.jpg"};

		var items = [turntable,motorcycle,lego,album,xbox];

		//window.onload = function() { //this makes the first element of the dropdown menu blank
		//	document.getElementById("forwhom").selectedIndex = -1;
		//}

		function printToPage() {
			var randIndex = Math.floor(Math.random()*items.length);
			var siteRef = items[randIndex].item_url;
    		document.getElementById("demo").innerHTML = items[randIndex].item_name;
			document.getElementById("demo").href = siteRef;
			document.getElementById("demo_img").src = items[randIndex].item_pic;
		}

		function printToPage_old() {
			var randIndex = Math.floor(Math.random()*items.length);
			var siteRef = "www.amazon.com/" + items[randIndex].replace(/ /g,"");
    		document.getElementById("demo").innerHTML = items[randIndex];
			document.getElementById("demo").href = siteRef;
			document.getElementById("myFrame").src = siteRef;
		}