<?php

	$curl = curl_init();

	curl_setopt_array($curl, array(
	  CURLOPT_URL => "http://api.rajaongkir.com/starter/province",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 30,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "GET",
	  CURLOPT_HTTPHEADER => array(
		"key: 8e1002008be8c6652e5fc397d8043ce3"
	  ),
	));

	$response = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);

	/* if ($err) {
	  echo "cURL Error #:" . $err;
	} else {
	  echo $response;
	} */
	$response = json_decode($response)->rajaongkir->results;
	//print_r($response);
?>
<html>
	<body>
		<form>
			<h2>origin</h2>
			<div>
				provinsi
				<select name="provinsi" id="select_provinsi_origin">
					<option value="">-pilih provinsi-</option>
				<?php
					foreach($response as $data){
						echo "<option value='{$data->province_id}'>{$data->province}</option>";
					}
				?>
				</select>
			</div>
			
			<br/>
			
			<div id="kabupaten_origin">
				kabupaten
				<select name="kabupaten" id="select_kabupaten_origin">
				</select>
			</div>
			<hr/>
			<h2>destination</h2>
			<div>
				provinsi
				<select name="provinsi" id="select_provinsi">
					<option value="">-pilih provinsi-</option>
				<?php
					foreach($response as $data){
						echo "<option value='{$data->province_id}'>{$data->province}</option>";
					}
				?>
				</select>
			</div>
			
			<br/>
			
			<div id="kabupaten">
				kabupaten
				<select name="kabupaten" id="select_kabupaten">
				</select>
			</div>
			
			<hr/>
			
			<div id="courier">
				courier
				<select name="courier" id="select_courier">
					<option value="jne">JNE</option>
					<option value="pos">POS</option>
					<option value="tiki">TIKI</option>
				</select>
				<br/>
				<br/>
				berat
				<input type="number" name="weight" id="weight" required> gram
				<br/>
				<br/>
				<input type="submit" name="submit" id="submit">
			</div>
		</form>
		<hr/>
		<div id="service">
			<p>Origin: <span id="origin"></span></p>
			<p>Destinaton: <span id="destination"></span></p>
			service
			<select name="service" id="select_service">
			</select>
			<p>Harga: <span id="harga"></span></p>
		</div>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script>
			$('#kabupaten').hide();
			$('#kabupaten_origin').hide();
			$('#courier').hide();
			$('#service').hide();
			$('#select_provinsi_origin').on('change', function() {
				var id = this.value;
				if(id != ''){
					$('#kabupaten_origin').show();
					$('#select_kabupaten_origin').find('option').remove().end();
					$.ajax({
						type: 'GET', 
						url: 'http://localhost:8080/rajaongkir_api/city.php', 
						data: { id: id }, 
						dataType: 'json',
						success: function (data) { 
							$.each(data, function(key, value) {   
								 $('#select_kabupaten_origin').append($("<option></option>")
									.attr("value",value.city_id)
									.text(value.type+' '+value.city_name));
							});
							//console.log(data);
							$('#select_kabupaten_origin').change();
						}
					});
				}else{
					$('#kabupaten_origin').hide();
				}
			});
			$('#select_provinsi').on('change', function() {
				var id = this.value;
				if(id != ''){
					$('#kabupaten').show();
					$('#courier').show();
					$('#select_kabupaten').find('option').remove().end();
					$.ajax({
						type: 'GET', 
						url: 'http://localhost:8080/rajaongkir_api/city.php', 
						data: { id: id }, 
						dataType: 'json',
						success: function (data) { 
							$.each(data, function(key, value) {   
								 $('#select_kabupaten').append($("<option></option>")
									.attr("value",value.city_id)
									.text(value.type+' '+value.city_name));
							});
							//console.log(data);
							$('#select_kabupaten').change();
						}
					});
				}else{
					$('#courier').hide();
					$('#kabupaten').hide();
				}
			});
			$("form").on("submit", function(){
				var city_origin = $('#select_kabupaten_origin').find(":selected").val();
				var city = $('#select_kabupaten').find(":selected").val();
				var courier = $('#select_courier').find(":selected").val();
				var weight = $('#weight').val();
				$.ajax({
					type: 'GET', 
					url: 'http://localhost:8080/rajaongkir_api/cost.php', 
					data: { origin: city_origin, destination: city, weight: weight, courier: courier}, 
					dataType: 'json',
					success: function (data) {
						$('#destination').html(destination);
						var origin = data.origin_details.type+' '+data.origin_details.city_name+', '+data.origin_details.province;
						
						$('#origin').html(origin);
						var destination = data.destination_details.type+' '+data.destination_details.city_name+', '+data.destination_details.province;
						$('#destination').html(destination);
						
						$('#service').show();
						var service = data.results[0].costs;
						$('#select_service').empty();
						$.each(service, function(key, value) {   
							 $('#select_service').append($("<option></option>")
								.attr("value",value.cost[0].value)
								.text(value.service+', estimasi: '+value.cost[0].etd));
								//console.log(value);
						});
						//console.log(data);
					}
				});
				return false;
			});
			$('#select_service').on('change', function() {
				var harga = this.value;
				$('#harga').html(harga);
			});
		</script>
	</body>
</html>