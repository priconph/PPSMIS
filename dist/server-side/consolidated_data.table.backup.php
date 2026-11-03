<?php
    /*
     * Script:    DataTables server-side script for PHP and MySQL
     * Copyright: 2010 - Allan Jardine
     * License:   GPL v2 or BSD (3-point)
     */
    
    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Easy set variables
     */
    
    /* Array of database columns which should be read and sent back to DataTables. Use a space where
     * you want to insert a non-database field (for example a counter or static image)
     */
    // error_reporting(E_ALL);
    // ini_set('display_errors', 1);
    $previous_month     = date('n', strtotime($_GET['data_year'].'-'.$_GET['data_month'].'-01 -1 months'));
    $previous_year      = date('Y', strtotime($_GET['data_year'].'-'.$_GET['data_month'].'-01 -1 months'));
    $array_where        = array();    
    $aColumns           = array('B.ProductLine',
                                'B.PartNumber',
                                'B.MaterialType',
                                'C.pmid_mix_value AS current_mix_value',
                                'C.pmid_virgin_value AS current_virgin_value',
                                'C.pmid_actual_value AS current_actual_value',
                                'D.mc_whs_remaining AS current_whs_remaining',
                                'D.mc_boh_price',
                                'D.mc_received_price',
                                'D.mc_eoh_price',
                                'D.mc_received_quantity',
                                'D.mc_adjustment',
                                'D.mc_used_qty',
                                'E.mc_whs_remaining as previous_whs_remaining',
                                'F.pmid_mix_value AS previous_mix_value',
                                'F.pmid_virgin_value AS previous_virgin_value',
                                'F.pmid_actual_value AS previous_actual_value');
    $sIndexColumn       = "id";
    $sTable             = 't_material_cost AS A LEFT JOIN db_pps.tbl_Warehouse AS B ON A.mc_material_code=B.PartNumber LEFT JOIN t_production_monthly_inventory_data AS C ON A.mc_material_code=C.pmid_material_code AND C.pmid_month="'.$_GET['data_month'].'" AND C.pmid_year="'.$_GET['data_year'].'" LEFT JOIN t_material_cost AS D ON A.mc_id=D.mc_id AND D.mc_month="'.$_GET['data_month'].'" AND D.mc_year="'.$_GET['data_year'].'" LEFT JOIN t_material_cost AS E ON A.mc_id=E.mc_id AND E.mc_month="'.$previous_month.'" AND E.mc_year="'.$previous_year.'" LEFT JOIN t_production_monthly_inventory_data AS F ON A.mc_material_code=F.pmid_material_code AND F.pmid_month="'.$previous_month.'" AND F.pmid_year="'.$previous_year.'"';
    $array_where[]      = ' A.mc_logdel=0';
    
    /* Database connection information */
    require_once('./connect.sside.php');
    
    
    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * If you just want to use the basic configuration for DataTables with PHP server-side, there is
     * no need to edit below this line
     */
    
    /* 
     * MySQL connection
     */
    $gaSql['link'] =  mysql_pconnect( $gaSql['server'], $gaSql['user'], $gaSql['password']  ) or
        die( 'Could not open connection to server' );
    
    mysql_select_db( $gaSql['db'], $gaSql['link'] ) or 
        die( 'Could not select database '. $gaSql['db'] );
    
    
    /* 
     * Paging
     */
    $sLimit = "";
    if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
    {
        $sLimit = "LIMIT ".mysql_real_escape_string( $_GET['iDisplayStart'] ).", ".
            mysql_real_escape_string( $_GET['iDisplayLength'] );
    }
        
    /*
     * Ordering
     */
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
    
    
    /* 
     * Filtering
     * NOTE this does not match the built-in DataTables filtering which does it
     * word by word on any field. It's possible to do here, but concerned about efficiency
     * on very large tables, and MySQL's regex functionality is very limited
     */
     
     /* wag gagalawin yung sWhere default to para sa searchbox ng datatables check mo sa baba kung pano gagawin mo :) */
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
    
    /* Individual column filtering */
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
    
    /* 
        dito ka mag add ng where mo, una check mo kung may laman na yung $sWhere pag wala append mo yung where mo na meron
        kasama where kapag naman may laman na AND na syempre diba :)
    */
    
    if( count($array_where) !=0 ){
        if($sWhere == ""){
            $sWhere .= "WHERE  ";
        }else{
            $sWhere .= "AND ";
        }
        $sWhere .= implode(' AND ',$array_where);
    }
    


    // $date_start = $_GET['date_start'];
    // $date_end    = $_GET['date_end'];
    
    // $sWhere .= "((date BETWEEN '" . $date_start . "' AND '" . $date_end . "') OR (date_out BETWEEN '" . $date_start . "' AND '" . $date_end . "')) AND `empno` LIKE '%".$empno."%'";
    
    /*
     * SQL queries
     * Get data to display
     */
    $sQuery = "
        SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
        FROM   $sTable
        $sWhere
        $sOrder
        $sLimit
    ";
    // mysql_query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
    $rResult = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
    
    $iTotal = mysql_num_rows($rResult);
    // echo $sQuery;
    // echo "limit: ".$sLimit." : ".$sQuery."<hr>";
    // $query_used = $sQuery;
    // echo $query_used;
    
    /* Data set length after filtering */
    // $sQuery = "
    //     SELECT FOUND_ROWS()
    // ";
    // $rResultFilterTotal = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
    // $aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
    // $iFilteredTotal = $aResultFilterTotal[0];
    
    /* Total data set length */
    // $sQuery = "
    //     SELECT COUNT(".$sIndexColumn.")
    //     FROM   $sTable
    // ";
    // $rResultTotal = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
    // $aResultTotal = mysql_fetch_array($rResultTotal);
    // $iTotal = $aResultTotal[0];
    
    
    /*
     * Output
     */
    $output = array(
        "sEcho" => intval($_GET['sEcho']),
        "iTotalRecords" => $iTotal,
        "iTotalDisplayRecords" => $iTotal,
        "aaData" => array()
    );
    while($aRow = mysql_fetch_array($rResult)){
        $actual_value   = '';
        $row            = array();
        unset($row);  

        $row[] = $aRow['ProductLine'];
        $row[] = $aRow['PartNumber'];
        $row[] = $aRow['MaterialType'];
        $row[] = $aRow['mc_boh_price'];
        $row[] = $aRow['mc_received_price'];
        $row[] = $aRow['mc_eoh_price'];

        $row[] = $aRow['previous_whs_remaining'];
        if($aRow['previous_actual_value']!=''){
            $row[] = $aRow['previous_actual_value'];
            $row[] = $aRow['previous_whs_remaining']+$aRow['previous_actual_value'];
        }
        else{        
            $row[] = ($aRow['previous_mix_value']*.8)+$aRow['previous_virgin_value'];
            $row[] = (($aRow['previous_mix_value']*.8)+($aRow['previous_virgin_value']))+($aRow['previous_whs_remaining']);
        }

        $row[] = $aRow['mc_received_quantity'];
        $row[] = $aRow['mc_adjustment'];
        $row[] = $aRow['mc_used_qty'];

        $row[] = $aRow['current_whs_remaining'];
        if($aRow['current_actual_value']!=''){
            $row[] = $aRow['current_actual_value'];
            $row[] = $aRow['current_whs_remaining']+$aRow['current_actual_value'];
        }
        else{        
            $row[] = ($aRow['current_mix_value']*.8)+$aRow['current_virgin_value'];
            $row[] = (($aRow['current_mix_value']*.8)+($aRow['current_virgin_value']))+($aRow['current_whs_remaining']);
        }

        $row[] = $aRow['ProductLine'];
        $row[] = $aRow['ProductLine'];
        $row[] = $aRow['ProductLine'];
        $row[] = $aRow['ProductLine'];
        $row[] = $aRow['ProductLine'];
        $row[] = $aRow['ProductLine'];
        $row[] = $aRow['ProductLine'];
        $row[] = $aRow['ProductLine'];
        $row[] = $aRow['ProductLine'];

        array_push($output['aaData'],$row);
    }
    echo json_encode($output);
?>