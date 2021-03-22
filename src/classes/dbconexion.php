<?php

class DBConexion
{
	private $conexion = null;

	public function __construct($conexion)
	{
		$this->conexion = $conexion;
		unset($conexion);
	}

    public function executeQuery($sql)
    {
        $query = $this->conexion->Conectar();
        $resultado = $query->query($sql);

        if ($resultado->rowCount() == 0) {
            return false;
        }

		return $resultado->fetchAll(PDO::FETCH_ASSOC);
    }

	public function executePrepare($sql, array $params, $isSelect = null)
    {
	
        $query = $this->conexion->Conectar();
		$resultado = $query->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $datos = [];
		foreach($params as $key => $value){
			$datos[":{$key}"] = $value;
		}
		$respuesta = $resultado->execute($datos);
		if ($isSelect){
			return $resultado->fetchAll();
		}
		 return $respuesta;
		
    }
}


?>