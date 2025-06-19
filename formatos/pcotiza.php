



<?php



function getPlantilla($folio)
{



	include_once '../bd/conexion.php';

	$plantilla = "";

	
function mayusculasEspanol($texto) {
    return mb_strtoupper($texto, 'UTF-8');
}

	if ($folio != "") {
		$objeto = new conn();
		$conexion = $objeto->connect();


		$consulta = "SELECT * FROM vpresupuesto WHERE id_pres='$folio'";

		$resultado = $conexion->prepare($consulta);
		$resultado->execute();


		$data = $resultado->fetchAll(PDO::FETCH_ASSOC);

		foreach ($data as $dt) {
			$folio = $dt['id_pres'];

			$fecha = $dt['fecha_pres'];
			$idpros = $dt['id_pros'];
			$prospecto = $dt['nombre_pros'];
			$proyecto = $dt['nproyecto'];
			$manzana = $dt['nmanzana'];
			$lote = $dt['nlote'];
			$concepto = $proyecto . ' - ' . $manzana . ' - ' . $lote;
			$ubicacion = "";
			$total = $dt['totalpagar'];
			$tasa = $dt['tasa'];
			$importe = $dt['importe'];
			$descuento = $dt['descuento'];
			$valorop = $dt['valorop'];
			$enganche = $dt['enganche'];
			$nenganche = $dt['nenganche'];
			$msi = $dt['nmsi'];
			$mci = $dt['nmci'];
			$totalcapital = $dt['totalcapital'];
			$totalinteres = $dt['totalinteres'];
			$totalpagar = $dt['totalpagar'];
			$superficie = $dt['superficie'];
			$preciom= $dt['preciom'];
			$fondo = $dt['fondo'];
			$frente = $dt['frente'];
			$tipo = $dt['tipo'];
			$descuentopor = $dt['descuentopor'];
			$enganchepor = $dt['enganchepor'];

		}






		$consultadet = "SELECT * FROM detalle_pres WHERE id_pres='$folio' ORDER BY id_reg";
		$resultadodet = $conexion->prepare($consultadet);
		$resultadodet->execute();
		$datadet = $resultadodet->fetchAll(PDO::FETCH_ASSOC);
	} else {
		echo '<script type="text/javascript">';
		echo 'window.location.href="../inicio.php";';
		echo '</script>';
	}

	$plantilla .= '
<body>

	
		<header class="">
           <table>
           <tr>
           <td>
            <div class="logo_factura">
                <img style="width:180px;" src="../img/logoVerde.png">
            </div>
        </td>
        <td class="textcenter">
            <div class="info_empresa" >
                <p><span class="empresa"><b>INMOBILIARIA BOSQUE DE LAS ANIMAS S.A. DE C.V.<b></span><br>
                     BLVD. CRISTOBAL COLON 5 INT 501<br>
                COL FUENTE DE LAS ANIMAS<br>
                Tel: (228) 8137981<br>
               
            </div>
        </td>
        <td class="round">
            <div class=" info_factura">
                
                <p>No. Presupuesto: <strong>' . $folio . '</strong></p>
                <p>Fecha: ' . $fecha . '</p> 
				<p>T.I. Anual: ' . $tasa . '%</p>

            </div>
            </td>
    			<tr>				
			</table>
        </header>
        <main>
		<br>
		<div>
			<table class="factura_cliente">
				<tr>
					<td class="info_cliente">
						<div class="round">
							<span class="encabezado">CLIENTE: <b>' . mayusculasEspanol($prospecto) . '</b> </span><br>
							<span class="encabezado">INMUEBLE: <b>' . mayusculasEspanol($concepto) . '</b> </span><br>
						<table class="detalle_pres">
								<thead>
									<tr>
										<th class="textcenter">Superficie</th>
										<th class="textcenter">Precio m2</th>
										<th class="textcenter">Frente</th>
										<th class="textcenter">Fondo</th>
										
										<th class="textcenter">Tipo</th>
										<th class="textcenter">Valor Operación</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td class="textcenter">' . number_format($superficie, 2) . ' m2</td>
										<td class="textright">$ ' . number_format($preciom, 2) . '</td>
										<td class="textcenter">' . number_format($frente, 2) . ' m</td>
										<td class="textcenter">' . number_format($fondo, 2) . ' m</td>
										
										<td class="textcenter">' . $tipo . '</td>
										<td class="textright">$ ' . number_format($valorop, 2) . '</td>
									</tr>
								</tbody>
							</table>

							<table class="detalle_pres">
								<thead>
									<tr>
										<th class="textcenter" >Importe</th>
										<th class="textcenter">% Desc</th>
										<th class="textcenter">Descuento</th>
										<th class="textcenter">Importe Total</th>
										<th class="textcenter">% Eng</th>
										<th class="textcenter">Enganche</th>
										<th class="textcenter">M-Eng.</th>
										<th class="textcenter">MSI</th>
										<th class="textcenter">MCI</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td class="textright">$ ' . number_format($importe, 2) . '</td>
										<td class="textcenter">' . number_format($descuentopor, 2) . '%</td>
										<td class="textright">$ ' . number_format($descuento, 2) . '</td>
										<td class="textright">$ ' . number_format($valorop, 2) . '</td>
										<td class="textcenter">' . number_format($enganchepor, 2) . '%</td>
										<td class="textright">$ ' . number_format($enganche, 2) . '</td>
										<td class="textcenter">' . number_format($nenganche, 0) . '</td>
										<td class="textcenter"> ' . number_format($msi, 0) . '</td>
										<td class="textcenter"> ' . number_format($mci, 0) . '</td>
									</tr>	
								</tbody>
							</table>
									

							
							
						</div>
					</td>

				</tr>
			</table>
		</div>
		<div>
			<table class="factura_detalle">
				<thead class="" style="width:100%">
					<tr>
						<th class="textcenter" style="width:10%">No.</th>
						<th class="textcenter">Fecha</th>
						<th class="textcenter">Capital</th>
						<th class="textcenter">Interes</th>
						<th class="textcenter">Total</th>
						<th class="textcenter">Tipo</th>
						<th class="textcenter">Saldo</th>

					</tr>
				</thead>

                <tbody class="detalle_productos">';
	foreach ($datadet as $row) {
		$plantilla .= '<tr>
							<td>' . $row['id_reg'] . '</td>
							<td>' . $row['fecha'] . '</td>
							<td class="textright">$ ' . number_format($row['capital'], 2) . '</td>
							<td class="textright">$ ' . number_format($row['interes'], 2) . '</td>
							<td class="textright">$ ' . number_format($row['importe'], 2) . '</td>
							<td class="textright">' . $row['tipo'] . '</td>
							<td class="textright">$ ' . number_format($row['saldo'], 2) . '</td>
						</tr>';
	}
	$plantilla .= '</tbody>
				<br>
			</table>

			<table class="factura_detalle" style="width:100%">
				<thead>
					<tr>
						<th class="textcenter">Total Capital</th>
						<th class="textcenter">Total Interés</th>
						<th class="textcenter">Total a Pagar</th>	
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="textright">$ ' . number_format($totalcapital, 2) . '</td>
						<td class="textright">$ ' . number_format($totalinteres, 2) . '</td>
						<td class="textright">$ ' . number_format($totalpagar, 2) . '</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div>
		
		</div>
	</main>


</body>
';
	return $plantilla;
}
