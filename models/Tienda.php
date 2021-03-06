<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models;

/**
 * Description of Tienda
 *
 * @author root
 */

use yii;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;

class Tienda {
    //put your code here
    
    public static function getMarcaTienda(){
        $con = \Yii::$app->db_tienda;
        $sql="SELECT A.ids_mar,A.cod_mar,A.nom_mar,
                (SELECT COUNT(*) FROM " . $con->dbname . ".productos B WHERE B.est_log=1 AND B.ids_mar=A.ids_mar) rcount
            FROM " . $con->dbname . ".marca A WHERE A.est_log=1 AND A.est_web=1;";
        $comando = $con->createCommand($sql);
        //$comando->bindParam(":med_id", $ids, \PDO::PARAM_INT);
        return $comando->queryAll();
    }
    
    public static function getSeccionTienda(){
        $con = \Yii::$app->db_tienda;
        $sql="SELECT ids_cat,nom_cat FROM " . $con->dbname . ".categorias WHERE ids_cat=ids_scat AND est_log=1 ORDER BY orden;";
        $comando = $con->createCommand($sql);
        //$comando->bindParam(":med_id", $ids, \PDO::PARAM_INT);
        return $comando->queryAll();
    }
    
    public static function getSubNivelTienda($ids){
        $con = \Yii::$app->db_tienda;
        $sql="SELECT ids_cat,nom_cat  FROM " . $con->dbname . ".categorias "
                . " WHERE ids_scat=:ids AND ids_cat<>ids_scat AND est_log=1 ORDER BY ids_cat;";
        //echo $sql;
        $comando = $con->createCommand($sql);
        $comando->bindParam(":ids", $ids, \PDO::PARAM_INT);
        $result=$comando->queryAll();
        if ($result === false)
            return 0; //en caso de que existe problema o no retorne nada tiene 1 por defecto 
        return $result;
    }
    
    public static function getNivelSuperior($ids){
        $con = \Yii::$app->db_tienda;
        $sql="SELECT ids_cat,ids_scat,nom_cat  FROM " . $con->dbname . ".categorias "
                . " WHERE ids_cat=:ids AND est_log=1 ORDER BY ids_cat;";
        $comando = $con->createCommand($sql);
        $comando->bindParam(":ids", $ids, \PDO::PARAM_INT);
        $result=$comando->queryAll();
        if ($result === false)
            return 0; //en caso de que existe problema o no retorne nada tiene 1 por defecto 
        return $result;
    }
    
    public static function getNivelTienda($ids){
        $rawData = array();
        $rawData=Tienda::getSubNivelTienda($ids);
        if($rawData!=0){
            for ($i = 0; $i < sizeof($rawData); $i++) {
                $rawData[$i]['subnivel']=Tienda::getSubNivelTienda($rawData[$i]['ids_cat']);
            }
        }
        return $rawData;
    }
    
    //Obtiene el menu completo de datos principal
    public static function getMenuData(){
        $rawData=array();
        $con = \Yii::$app->db_tienda;
        $sql="SELECT ids_cat,nom_cat FROM " . $con->dbname . ".categorias WHERE ids_cat=ids_scat AND est_log=1 ORDER BY orden;";
        $comando = $con->createCommand($sql);
        //$comando->bindParam(":med_id", $ids, \PDO::PARAM_INT);
        $rawData= $comando->queryAll();
        if($rawData!=0){            
            for ($i = 0; $i < sizeof($rawData); $i++) {
                $ids=$rawData[$i]['ids_cat'];
                $rawData[$i]['nivel']=Tienda::getNivelTienda($ids);
            }
            
        }
        return $rawData;
        //Utilities::putMessageLogFile($rawData);
    }
    
    

    public static function getProductoTienda($data){
        $arroout = array();
        /*$tipOrderby="ASC";
        $page=1;//Valor por defecto 1
        $idsCat=0;//Valor por defecto 0
        $desCom="";
        $tCount=Tienda::getCountProductoTienda();
        //Utilities::putMessageLogFile($data);
        if(isset($data['page'])){$page=$data['page'];}
        if(isset($data['idsCat'])){$idsCat=$data['idsCat'];}
        if(isset($data['desCom'])){$desCom=$data['desCom'];}
        if(isset($data['orderBy'])){$tipOrderby=($data['orderBy']==2)?"DESC":"ASC";}

        $rowsPerPage = \Yii::$app->params['pagePro'];
        $offset = ($page - 1) * $rowsPerPage;
        $con = \Yii::$app->db_tienda;
        $sql="SELECT A.ids_pro,A.cod_art,A.des_com,B.p_venta,A.ruta_img
                FROM " . $con->dbname . ".productos A
                  INNER JOIN " . $con->dbname . ".precios B
                    ON A.ids_pro=B.ids_pro
              WHERE A.est_log=1 ";
        $sql.=($idsCat!=0)?" AND A.ids_cat=$idsCat":"";  
        $sql.=($desCom!="")?" AND A.des_com LIKE '%$desCom%' ":"";
        $sql.=" ORDER BY A.des_com ". $tipOrderby;
        $sql.=" LIMIT ".$offset.", ".$rowsPerPage;

        $comando = $con->createCommand($sql);
        
        //$comando->bindParam(":med_id", $ids, \PDO::PARAM_INT);
        $rawData=$comando->queryAll();
        //Utilities::putMessageLogFile($rawData);
        
        //Tienda::existeImgLista($rawData);

        $arroout["status"] = TRUE;
        //$arroout["error"] = null;
        //$arroout["message"] = null;
        $arroout["trows"] = $tCount;//count($rawData);
        $arroout["data"] = $rawData;*/
        return $arroout;
        
        //return $comando->queryAll();
        
    }
    
    
    public static function getProductoTiendaIndex($data){
        //Utilities::putMessageLogFile($data);
        
        /*OBTIENE DESCUENTOS*/
        //Yii::$app->session->get("PB_iduser");
        //Se debe consultar la tabla usuario CLiente
        //UTIMPOR IDS=1 POR DEFECTO
        $ids=1;
        $precio=Productos::consultarCatPrecio($ids);
        $CampoVenta=\Yii::$app->params['p_venta'];
        $por_des=floatval($precio['por_des'])/100;
        /*FIN DESCUENTOS*/
        
        $arroout = array();
        $tipOrderby="ASC";
        $page=1;//Valor por defecto 1
        $desCom="";

        $idsCat=isset($data["codigo"]) ? base64_decode($data['codigo']) : "0";
        
        ///Utilities::putMessageLogFile("ids ".$data);
        //Utilities::putMessageLogFile("ids ".$idsCat);
        
        if(isset($data['page'])){$page=$data['page'];}
        //if(isset($data['idsCat'])){$idsCat=$data['idsCat'];}        
        if(isset($data['desCom'])){$desCom=$data['desCom'];}
        if(isset($data['order By'])){$tipOrderby=($data['orderBy']==2)?"DESC":"ASC";}
        $rowsPerPage = \Yii::$app->params['pagePro'];
        $offset = ($page - 1) * $rowsPerPage;
        $con = \Yii::$app->db_tienda;
        
      
        $sql="SELECT A.ids_pro,A.cod_art,A.des_com,(A.$CampoVenta-(A.$CampoVenta*$por_des)) p_venta,A.ruta_img,
             A.ids_cat
                FROM " . $con->dbname . ".productos A                  
              WHERE A.est_log=1 ";
         
        if($desCom!=""){
            $sql.=" AND A.des_com LIKE '%$desCom%' ";
        }else{
            $sql.=($idsCat!=0)?" AND A.ids_cat=$idsCat":"";  
        }
        //$sql.=($desCom!="")?" AND A.des_com LIKE '%$desCom%' ":"";
        
        //Utilities::putMessageLogFile($sql);
        //echo $sql;

        $comando = $con->createCommand($sql);        
        //$comando->bindParam(":med_id", $ids, \PDO::PARAM_INT);
        $rawData=$comando->queryAll();
        //PARA CONSULTAR PAGINADO
        $arroout["trows"] = count($rawData);
        //Utilities::putMessageLogFile(count($rawData));
        
        
        //PARA CONSULTAR DATOS        
        $sql.=" ORDER BY A.des_com ". $tipOrderby;
        $sql.=" LIMIT ".$offset.", ".$rowsPerPage;
        $comando = $con->createCommand($sql);
        //Utilities::putMessageLogFile($sql);
        //echo $sql;
        $rawData=$comando->queryAll();
        $arroout["data"] = $rawData;
        
        //Utilities::putMessageLogFile($rawData);        
        //Tienda::existeImgLista($rawData);
        $arroout["idscat"] = $idsCat;
        $arroout["status"] = TRUE;
        //$arroout["error"] = null;
        //$arroout["message"] = null;
        
        return $arroout;
    }

    
    
    public static function existeImgLista($rawData) {
        //prueba rutatas
        //Utilities::putMessageLogFile(sizeof("PARTE2"));
        //Utilities::putMessageLogFile(sizeof($rawData));
        
        for ($i = 0; $i < sizeof($rawData); $i++) {
            $nombre_fichero = \Yii::getAlias('@webroot').'/img/productos/'.$rawData[$i]['cod_art'].'_P-01.jpg';
            //$rawData[$i]['cod_art'] = Tienda::getSubNivelTienda($rawData[$i]['ids_cat']);
            if (file_exists($nombre_fichero)) {
                //echo "El fichero $nombre_fichero existe";
                //Utilities::putMessageLogFile($nombre_fichero);
            } else {
                //echo "El fichero ".$rawData[$i]['cod_art']." no existe<br>";
                //Utilities::putMessageLogFile($nombre_fichero);
                //Utilities::putMessageLogFile("El fichero ".$rawData[$i]['cod_art']." no existe<br>");
            }
        }
    }

    public static function getProductoTiendaMasVendidos($tipo){
        //$arroout = array();
        /*OBTIENE DESCUENTOS*/
        //Yii::$app->session->get("PB_iduser");
        $precio=Productos::consultarCatPrecio($ids);
        $CampoVenta=\Yii::$app->params['p_venta'];
        $por_des=floatval($precio['por_des'])/100;
        /*FIN DESCUENTOS*/
        $con = \Yii::$app->db_tienda;        
        
        /*$sql="SELECT B.ids_pro,B.cod_art,B.des_com,C.p_venta,B.ruta_img
                FROM " . $con->dbname . ".productos_sugeridos A
                  INNER JOIN (" . $con->dbname . ".productos B
                      INNER JOIN " . $con->dbname . ".precios C ON B.ids_pro=C.ids_pro)
                    ON A.ids_pro=B.ids_pro
              WHERE A.est_log=1 AND A.tip_psug=:tipo ";
        $sql.=" ORDER BY A.ids_psug ASC ";*/
        
        $sql="SELECT B.ids_pro,B.cod_art,B.des_com,(B.$CampoVenta-(B.$CampoVenta*$por_des)) p_venta,B.ruta_img
                FROM " . $con->dbname . ".productos_sugeridos A
                  INNER JOIN " . $con->dbname . ".productos B                      
                    ON A.ids_pro=B.ids_pro
              WHERE A.est_log=1 AND A.tip_psug=:tipo ";
        $sql.=" ORDER BY A.ids_psug ASC ";
        
        //Utilities::putMessageLogFile($sql);
        
        $comando = $con->createCommand($sql);        
        //$comando->bindParam(":med_id", $ids, \PDO::PARAM_INT);
        $comando->bindParam(":tipo", $tipo, \PDO::PARAM_STR);
        //$rawData=$comando->queryAll();
        return $comando->queryAll();
        //Utilities::putMessageLogFile($rawData);
       
        
    }
    
    public static function getCountProductoTienda(){
        $con = \Yii::$app->db_tienda;
        $sql="SELECT COUNT(*) tpro FROM " . $con->dbname . ".productos "
                . "WHERE est_log=1 AND est_web=0 ";
        $comando = $con->createCommand($sql);
        //$comando->bindParam(":med_id", $ids, \PDO::PARAM_INT);
        //$rawData=$comando->queryAll();
        $rawData=$comando->queryScalar();
        if ($rawData === false)
            return 0; //en caso de que existe problema o no retorne nada tiene 1 por defecto 
        return $rawData;
    }
    
    public static function getProductoDetalle($ids){
        /*OBTIENE DESCUENTOS*/
        //Yii::$app->session->get("PB_iduser");
        $precio=Productos::consultarCatPrecio($idCli);
        $CampoVenta=\Yii::$app->params['p_venta']; 
        $por_des=floatval($precio['por_des'])/100;
        /*FIN DESCUENTOS*/
        
        $con = \Yii::$app->db_tienda;        
        /*$sql="SELECT A.ids_pro,A.cod_art,A.des_com,B.p_venta,A.ruta_img,C.nom_cat,
		D.nom_mar
                FROM " . $con->dbname . ".productos A
                  INNER JOIN " . $con->dbname . ".precios B
                    ON A.ids_pro=B.ids_pro
                  INNER JOIN " . $con->dbname . ".categorias C
                        ON C.ids_cat=A.ids_cat
                  INNER JOIN " . $con->dbname . ".marca D
                        ON D.cod_mar=A.cod_mar
              WHERE A.est_log=1  AND A.ids_pro=:ids ;";*/
        
        //revisar no cambiar el valor
        $sql="SELECT A.ids_pro,A.cod_art,A.des_com,(A.$CampoVenta-(A.$CampoVenta*$por_des)) p_venta,A.ruta_img,C.nom_cat,
		D.nom_mar
                FROM " . $con->dbname . ".productos A                  
                  INNER JOIN " . $con->dbname . ".categorias C
                        ON C.ids_cat=A.ids_cat
                  INNER JOIN " . $con->dbname . ".marca D
                        ON D.cod_mar=A.cod_mar
              WHERE A.est_log=1  AND A.ids_pro=:ids ;";
        //Utilities::putMessageLogFile($sql);
        $comando = $con->createCommand($sql);
        $comando->bindParam(":ids", $ids, \PDO::PARAM_INT);
        return $comando->queryAll();
    }
    
    public function retornarBuscArticulo($valor, $op) {
        $con = \Yii::$app->db_tienda; 
        //$rawData = array();
        //Patron de Busqueda
        /* http://www.mclibre.org/consultar/php/lecciones/php_expresiones_regulares.html */
        $patron = "/^[[:digit:]]+$/"; //Los patrones deben empezar y acabar con el carácter / (barra).
        if (preg_match($patron, $valor)) {
            $op = "COD"; //La cadena son sólo números.
        } else {
            $op = "NOM"; //La cadena son Alfanumericos.
            //Las separa en un array 
            $aux = explode(" ", $valor);
            $condicion = " ";
            for ($i = 0; $i < count($aux); $i++) {
                //Crea la Sentencia de Busqueda
                $condicion .=" AND (des_com LIKE '%$aux[$i]%' OR cod_art LIKE '%$aux[$i]%' ) ";
                //$condicion .=" AND des_com LIKE '%$aux[$i]%' ";
            }
        }

        $sql = "SELECT ids_pro ids,cod_art codigo,des_com nombre "
                    . " FROM " . $con->dbname . ".productos "
                    . " WHERE est_log=1  ";

        switch ($op) {
            case 'COD':
                $sql .=" AND cod_art LIKE '%$valor%' ";
                break;
            case 'NOM':
                $sql .=$condicion;
                break;
            default:
        }
        //\Yii::$app->params['copyright']
        $sql .= " LIMIT " . \Yii::$app->params['limitRow'] ;
        //Utilities::putMessageLogFile($sql);
        //echo $sql;
        $comando = $con->createCommand($sql);
        //$comando->bindParam(":ids", $ids, \PDO::PARAM_INT);
        //$rawData=$comando->queryAll();
        return $comando->queryAll();
        
        //$rawData = $con->createCommand($sql)->queryAll();
        //$con->active = false;
        //return $rawData;
    }
    
}
