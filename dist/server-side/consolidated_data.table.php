<?php
    // error_reporting(E_ALL);
    // ini_set('display_errors', 1);

    require_once('./connect.sside.php');
    require_once('../handler/query.php');

    $previous_month     = date('n', strtotime($_GET['data_year'].'-'.$_GET['data_month'].'-01 -1 months'));
    $previous_year      = date('Y', strtotime($_GET['data_year'].'-'.$_GET['data_month'].'-01 -1 months'));
   
    $result             = '';
    $array_fields       = array('cd_start_date','cd_end_date');
    $table              = 't_cutoff_dates';
    $joins              = '';
    $sql_where          = 'WHERE cd_month="'.$previous_month.'" AND cd_year="'.$previous_year.'"';
    $sql_order          = '';
    $sql_limit          = '';
    $result             = db_ppsmis::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
    if($row = mysqli_fetch_array($result)){
        $previous_start_date   = $row['cd_start_date'];
        $previous_end_date     = $row['cd_end_date'];
    }

    $result             = '';
    $array_fields       = array('cd_start_date','cd_end_date');
    $table              = 't_cutoff_dates';
    $joins              = '';
    $sql_where          = 'WHERE cd_month="'.$_GET['data_month'].'" AND cd_year="'.$_GET['data_year'].'"';
    $sql_order          = '';
    $sql_limit          = '';
    $result             = db_ppsmis::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
    if($row = mysqli_fetch_array($result)){
        $current_start_date   = $row['cd_start_date'];
        $current_end_date     = $row['cd_end_date'];
    }

    $array_where        = array();    
    $aColumns           = array('A.mc_id',
                                'A.mc_boh_price',
                                'A.mc_received_price',
                                'A.mc_eoh_price',
                                'B.ProductLine',
                                'B.PartNumber',
                                'B.MaterialType',
                                'C.wmid_whs_balance_amount AS boh_whs_balance_amount',
                                'C.wmid_prodn_balance_amount AS boh_prodn_balance_amount',
                                'D.wmid_whs_balance_amount AS eoh_whs_balance_amount',
                                'D.wmid_prodn_balance_amount AS eoh_prodn_balance_amount',
                                'D.wmid_ppc_adjustment',
                                'D.wmid_received_qty',
                                '(SELECT SUM(`In`) FROM db_pps.tbl_WarehouseTransaction AS E WHERE E.fkid=A.mc_material_id AND (`E`.`In` <> "0.00" OR `E`.`Out` <> "0.00")) as total_received',
                                '(SELECT Eoh FROM db_pps.tbl_WarehouseTransaction AS F WHERE F.fkid=A.mc_material_id ORDER BY `F`.`Date` DESC,`F`.`pkid` DESC LIMIT 1) as tbl_warehouse_data_boh',
                                '(SELECT Eoh FROM db_pps.tbl_WarehouseTransaction AS G WHERE G.fkid=A.mc_material_id ORDER BY `G`.`Date` DESC,`G`.`pkid` DESC LIMIT 1) as tbl_warehouse_data_eoh',
                                'H.pmid_mix_value AS previous_month_pmid_mix_value',
                                'H.pmid_virgin_value AS previous_month_pmid_virgin_value',
                                'H.pmid_actual_value AS previous_month_pmid_actual_value',
                                'I.pmid_mix_value AS current_month_pmid_mix_value',
                                'I.pmid_virgin_value AS current_month_pmid_virgin_value',
                                'I.pmid_actual_value AS current_month_pmid_actual_value');
    $sIndexColumn       = 'mc_id';
    $sTable             = 't_material_cost AS A'; // Material Prices(Current Month)
    $sTable            .= ' LEFT JOIN t_material_cost AS AA ON A.mc_material_id=AA.mc_material_id AND AA.mc_logdel=0 AND A.mc_eoh_price=AA.mc_eoh_price';
    $sTable            .= ' LEFT JOIN db_pps.tbl_Warehouse AS B ON A.mc_material_id=B.id';
    $sTable            .= ' LEFT JOIN t_consolidated_monthly_inventory_data AS C ON AA.mc_id=C.wmid_mc_id'; // BOH from t_consolidated_monthly_inventory_data
    $sTable            .= ' LEFT JOIN t_consolidated_monthly_inventory_data AS D ON A.mc_id=D.wmid_mc_id'; // EOH from t_consolidated_monthly_inventory_data
    $sTable            .= ' LEFT JOIN t_production_monthly_inventory_data AS H ON H.pmid_material_code=B.PartNumber AND H.pmid_logdel=0'; // Previous Month Material Balance from t_production_monthly_inventory_data
    $sTable            .= ' LEFT JOIN t_production_monthly_inventory_data AS I ON I.pmid_material_code=B.PartNumber AND I.pmid_logdel=0'; // Current Month Material Balance from t_production_monthly_inventory_data
    $array_where[]      = ' A.mc_logdel=0 ORDER BY B.MaterialType ASC, A.mc_id ASC';
    
    $gaSql['link'] =  mysql_pconnect( $gaSql['server'], $gaSql['user'], $gaSql['password']  ) or die( 'Could not open connection to server' );
    
    mysql_select_db( $gaSql['db'], $gaSql['link'] ) or die( 'Could not select database '. $gaSql['db'] );
    
    $sLimit = "";
    if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
    {
        $sLimit = "LIMIT ".mysql_real_escape_string( $_GET['iDisplayStart'] ).", ".
            mysql_real_escape_string( $_GET['iDisplayLength'] );
    }
        
    if ( isset( $_GET['iSortCol_0'] ) )
    {
        $sOrder = "ORDER BY  ";
        for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
        {
            if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
            {
                $sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
                    ".mysql_real_escape_string( $_GET['sSortDir_'.$i] ) .", ";
            }
        }
        
        $sOrder = substr_replace( $sOrder, "", -2 );
        if ( $sOrder == "ORDER BY" )
        {
            $sOrder = "";
        }
    }
    
    $sWhere = "";
    if ( $_GET['sSearch'] != "" )
    {
        $sWhere = "WHERE (";
        for ( $i=0 ; $i<count($aColumns) ; $i++ )
        {
            // if( in_array($aColumns[$i],$array_search) ){
            $sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ";
            // }
        }
        $sWhere = substr_replace( $sWhere, "", -3 );
        $sWhere .= ')';
    }
    
    for ( $i=0 ; $i<count($aColumns) ; $i++ )
    {
        if ( $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
        {
            if ( $sWhere == "" )
            {
                $sWhere = "WHERE ";
            }
            else
            {
                $sWhere .= " AND ";
            }
            $sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string($_GET['sSearch_'.$i])."%' ";
        }
    }
    
    if( count($array_where) !=0 ){
        if($sWhere == ""){
            $sWhere .= "WHERE  ";
        }else{
            $sWhere .= "AND ";
        }
        $sWhere .= implode(' AND ',$array_where);
    }
    

    $sQuery = "
        SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
        FROM   $sTable
        $sWhere
        $sOrder
        $sLimit
    ";
    $rResult = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
    // echo $sQuery;
    
    $iTotal = mysql_num_rows($rResult);

    $output = array(
        "sEcho" => intval($_GET['sEcho']),
        "iTotalRecords" => $iTotal,
        "iTotalDisplayRecords" => $iTotal,
        "aaData" => array()
    );
    $mc_id = '';
    while($aRow = mysql_fetch_array($rResult)){
        $row            = array();
        unset($row);  
        if($mc_id!=$aRow['mc_id']){
            $row[] = $aRow['ProductLine'];
            $row[] = $aRow['PartNumber'];
            $row[] = $aRow['MaterialType'];
            $row[] = '<span class="bg-color-c3d6f4">'.number_format($aRow['mc_boh_price'], 4, '.', ',').'</span>';                              //BOH U/P
            $row[] = '<span class="bg-color-c3d6f4">'.number_format($aRow['mc_received_price'], 4, '.', ',').'</span>';                         //RECEIVED U/P
            $row[] = '<span class="bg-color-c3d6f4">'.number_format($aRow['mc_eoh_price'], 4, '.', ',').'</span>';                              //EOH U/P

            if($aRow['boh_whs_balance_amount']==null||trim($aRow['boh_whs_balance_amount'])==''){                                               //BOH(QTY. BASED) Warehouse
                if($current_material_code==$aRow['PartNumber']){                                                                                //BOH(QTY. BASED) Warehouse
                    $for_total_whs_data_boh = 0;                                                                                                //BOH(QTY. BASED) Warehouse
                    $row[] = '<span class="bg-color-warning">INC.</span>';                                                                      //BOH(QTY. BASED) Warehouse
                }                                                                                                                               //BOH(QTY. BASED) Warehouse
                else{                                                                                                                           //BOH(QTY. BASED) Warehouse
                    $for_total_whs_data_boh = $aRow['tbl_warehouse_data_boh'];                                                                  //BOH(QTY. BASED) Warehouse
                    $row[] = '<span class="bg-color-f2e1b5">'.number_format($for_total_whs_data_boh, 2, '.', ',').'</span>';                    //BOH(QTY. BASED) Warehouse
                }                                                                                                                               //BOH(QTY. BASED) Warehouse
            }                                                                                                                                   //BOH(QTY. BASED) Warehouse   
            else{                                                                                                                               //BOH(QTY. BASED) Warehouse
                $for_total_whs_data_boh = $aRow['boh_whs_balance_amount'];                                                                      //BOH(QTY. BASED) Warehouse
                $row[] = '<span class="bg-color-f2e1b5">'.number_format($for_total_whs_data_boh, 2, '.', ',').'</span>';                        //BOH(QTY. BASED) Warehouse
            }                                                                                                                                   //BOH(QTY. BASED) Warehouse

            if($aRow['boh_prodn_balance_amount']==null||trim($aRow['boh_prodn_balance_amount'])==''){                                           //BOH(QTY. BASED) Production
                if(floatval($aRow['previous_month_pmid_actual_value'])==0){                                                                     //BOH(QTY. BASED) Production
                    $for_total_prodn_data_boh = ($aRow['previous_month_pmid_mix_value']*.8)+$aRow['previous_month_pmid_virgin_value'];          //BOH(QTY. BASED) Production
                }                                                                                                                               //BOH(QTY. BASED) Production
                else{                                                                                                                           //BOH(QTY. BASED) Production
                    $for_total_prodn_data_boh = $aRow['previous_month_pmid_actual_value'];                                                      //BOH(QTY. BASED) Production
                }                                                                                                                               //BOH(QTY. BASED) Production
                                                                                                                                                //BOH(QTY. BASED) Production
                if($current_material_code==$aRow['PartNumber']){                                                                                //BOH(QTY. BASED) Production
                    $for_total_whs_data_boh = 0;                                                                                                //BOH(QTY. BASED) Production
                    $row[] = '<span class="bg-color-warning">INC.</span>';                                                                      //BOH(QTY. BASED) Production
                }                                                                                                                               //BOH(QTY. BASED) Production
                else{                                                                                                                           //BOH(QTY. BASED) Production
                    $row[] = '<span class="bg-color-f2e1b5">'.number_format($for_total_prodn_data_boh, 2, '.', ',').'</span>';                  //BOH(QTY. BASED) Production
                }                                                                                                                               //BOH(QTY. BASED) Production
            }                                                                                                                                   //BOH(QTY. BASED) Production
            else{                                                                                                                               //BOH(QTY. BASED) Production
                $for_total_prodn_data_boh = $aRow['boh_prodn_balance_amount'];                                                                  //BOH(QTY. BASED) Production
                $row[] = '<span class="bg-color-f2e1b5">'.number_format($for_total_prodn_data_boh, 2, '.', ',').'</span>';                      //BOH(QTY. BASED) Production
            }                                                                                                                                   //BOH(QTY. BASED) Production


            $total_data_boh = $for_total_whs_data_boh+$for_total_prodn_data_boh;                                                                //BOH(QTY. BASED) Total
            $row[] = '<span class="bg-color-b5ffc9">'.number_format($total_data_boh, 2, '.', ',').'</span>';                                    //BOH(QTY. BASED) Total

            if($aRow['wmid_received_qty']==null||trim($aRow['wmid_received_qty'])==''||$aRow['wmid_received_qty']=0){                           //Received
                if($current_material_code==$aRow['PartNumber']){                                                                                //Received
                    $row[] = '<span class="bg-color-warning">INC.</span>';                                                                      //Received
                }                                                                                                                               //Received
                else{                                                                                                     //Received
                    $row[] = $aRow['total_received'];   
                }                                                                                                                         //Received
            }                                                                                                                                   //Received
            else{                                                                                                                               //Received
                $row[] = '<span class="bg-color-f2e1b5">'.number_format($aRow['wmid_received_qty'], 2, '.', ',').'</span>';                                                      //Received
            }                                                                                                                                   //Received

            $row[] = '<span class="bg-color-f2e1b5">'.number_format($aRow['wmid_ppc_adjustment'], 2, '.', ',').'</span>';                       //Adjustment

            if($aRow['eoh_whs_balance_amount']==null||trim($aRow['eoh_whs_balance_amount'])==''){                                                                                                                           //EOH(QTY. BASED) Warehouse Data Variable
                $for_total_whs_data_eoh = $aRow['tbl_warehouse_data_eoh'];                                                                //EOH(QTY. BASED) Warehouse Data Variable
            }                                                                                                                                   //EOH(QTY. BASED) Warehouse Data Variable
            else{                                                                                                                               //EOH(QTY. BASED) Warehouse Data Variable
                $for_total_whs_data_eoh = $aRow['eoh_whs_balance_amount'];                                                                      //EOH(QTY. BASED) Warehouse Data Variable
            }                                                                                                                                   //EOH(QTY. BASED) Warehouse Data Variable

            if($aRow['eoh_prodn_balance_amount']==null||trim($aRow['eoh_prodn_balance_amount'])==''){                                           //EOH(QTY. BASED) Production Data Variable
                if(floatval($aRow['current_month_pmid_actual_value'])==0){                                                                      //EOH(QTY. BASED) Production Data Variable
                    $for_total_prodn_data_eoh = ($aRow['current_month_pmid_mix_value']*.8)+$aRow['current_month_pmid_virgin_value'];            //EOH(QTY. BASED) Production Data Variable
                }                                                                                                                               //EOH(QTY. BASED) Production Data Variable
                else{                                                                                                                           //EOH(QTY. BASED) Production Data Variable
                    $for_total_prodn_data_eoh = $aRow['current_month_pmid_actual_value'];                                                       //EOH(QTY. BASED) Production Data Variable
                }                                                                                                                               //EOH(QTY. BASED) Production Data Variable
            }                                                                                                                                   //EOH(QTY. BASED) Production Data Variable
            else{                                                                                                                               //EOH(QTY. BASED) Production Data Variable
                $for_total_prodn_data_eoh = $aRow['eoh_prodn_balance_amount'];                                                                  //EOH(QTY. BASED) Production Data Variable
            }                                                                                                                                   //EOH(QTY. BASED) Production Data Variable


            $total_data_used_qty = (($for_total_whs_data_boh+$for_total_prodn_data_boh)+$aRow['total_received']+$aRow['wmid_ppc_adjustment'])-($for_total_whs_data_eoh+$for_total_prodn_data_eoh);  //Used
            if($total_data_used_qty<=0){
                $row[] = '<span class="bg-color-b5ffc9"></span>';                       //Used
            }
            else{
                $row[] = '<span class="bg-color-b5ffc9">'.number_format($total_data_used_qty, 2, '.', ',').'</span>';                       //Used
            }                                                                              

            $row[] = '<span class="bg-color-f2e1b5">'.number_format($for_total_whs_data_eoh, 2, '.', ',').'</span>';                            //EOH(QTY. BASED) Warehouse Data Variable
            $row[] = '<span class="bg-color-f2e1b5">'.number_format($for_total_prodn_data_eoh, 2, '.', ',').'</span>';                          //EOH(QTY. BASED) Production Data Variable


            $total_data_eoh = $for_total_whs_data_eoh+$for_total_prodn_data_eoh;                                                                //EOH(QTY. BASED) Total Data Variable
            $row[] = '<span class="bg-color-b5ffc9">'.number_format($total_data_eoh, 2, '.', ',').'</span>';


            $row[] = '$ '.number_format($for_total_whs_data_boh*$aRow['mc_boh_price'], 2, '.', ',');
            $row[] = '$ '.number_format($for_total_prodn_data_boh*$aRow['mc_boh_price'], 2, '.', ',');


            $total_data_amount_based_boh = ($for_total_whs_data_boh*$aRow['mc_boh_price'])+($for_total_prodn_data_boh*$aRow['mc_boh_price']);
            $row[] = '<span class="bg-color-b5ffc9">$ '.number_format($total_data_amount_based_boh, 2, '.', ',').'</span>';
            $row[] = '$ '.number_format($aRow['mc_received_price']*$aRow['total_received'], 2, '.', ',');
            $row[] = '$ '.number_format($aRow['mc_eoh_price']*$aRow['wmid_ppc_adjustment'], 2, '.', ',');

            $total_whs_data_eoh_amount_based = $aRow['mc_eoh_price']*$for_total_whs_data_eoh;
            $total_prodn_data_eoh_amount_based = $aRow['mc_eoh_price']*$for_total_prodn_data_eoh;
            $total_data_amount_based_eoh = $total_whs_data_eoh_amount_based+$total_prodn_data_eoh_amount_based;

            $total_data_amount_based_used_qty = ((($for_total_whs_data_boh*$aRow['mc_boh_price'])+($for_total_prodn_data_boh*$aRow['mc_boh_price']))+($aRow['mc_received_price']*$aRow['total_received'])+($aRow['mc_eoh_price']*$aRow['wmid_ppc_adjustment']))-$total_data_amount_based_eoh;
            
            if($total_data_amount_based_used_qty<=0){
                $row[] = '<span class="bg-color-b5ffc9">$ 0.00</span>';            
            }
            else{
                $row[] = '<span class="bg-color-b5ffc9">$ '.number_format($total_data_amount_based_used_qty, 2, '.', ',').'</span>';
            }

            $row[] = '$ '.number_format($total_whs_data_eoh_amount_based, 2, '.', ',');
            $row[] = '$ '.number_format($total_prodn_data_eoh_amount_based, 2, '.', ',');
            $row[] = '<span class="bg-color-b5ffc9">$ '.number_format($total_data_amount_based_eoh, 2, '.', ',').'</span>';

            $current_received_qty =$aRow['wmid_received_qty'];
            $current_material_code = $aRow['PartNumber'];
            array_push($output['aaData'],$row);

        }
        $mc_id = $aRow['mc_id'];
    }
    echo json_encode($output);
?>