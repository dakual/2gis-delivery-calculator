<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>jQuery UI Autocomplete - Remote datasource</title>
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <style>
  .ui-autocomplete-loading {
    background: white url("images/ui-anim_basic_16x16.gif") right center no-repeat;
  }
  </style>
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script>
  $( function() {
    function log( message ) {
      $( "<div>" ).text( message ).prependTo( "#log" );
      $( "#log" ).scrollTop( 0 );
    }
	
	
    $( "#address" ).autocomplete({
      source: function( request, response ) {
        $.ajax({
          url: "https://catalog.api.2gis.com/3.0/suggests",
          dataType: "json",
          data: {
			q: request.term,
			suggest_type: 'address',
			region_id: '68',
			fields: 'items.point',
			location: '51.135902,71.422442',
			key: '<key>',
			sort: 'distance',
			type: 'building'
          },
          success: function( data ) {
			console.log(data);
			
			if(data.meta.code == 200) {
				response( $.map( data.result.items, function( item ) {
				  return {
					label: item.address_name,
					point: item.point,
				  }
				}));
			} else {
				var result = [{ label: "no results", value: response.term }];
                response(result);
			}
          }
        });
      },
      minLength: 3,
      select: function( event, ui ) {
        log( "Selected: " + ui.item.value + " lat: " + ui.item.point['lat'] + " lon: " + ui.item.point['lon'] );
		console.log(ui);
		
        var label = ui.item.label;
		if (label === "no results") {
			event.preventDefault();
        } else {
			if(ui.item.point != undefined) {
			$.post( "search.php", { lat: ui.item.point['lat'], lon: ui.item.point['lon'] }, function( data ) {
				console.log(data);	
				log("Distance: " + data.distance + ", Price: " + data.cost);
			});
				
			}
		}
      }	  
    });	

  } );
  </script>
</head>
<body>
 
<div class="ui-widget">
  <label for="address">Addess: </label>
  <input id="address">
</div>
 
<div class="ui-widget" style="margin-top:2em; font-family:Arial">
  Result:
  <div id="log" style="height: 200px; width: 300px; overflow: auto;" class="ui-widget-content"></div>
</div>
 
 
</body>
</html>
