<?php

class CSl_positionModelEx extends CSl_positionModel
{
  public function __construct()
  {
    parent::__construct();
    return true;
  }


  public function getApplication($pnCandidatePk, $pbActiveOnly = false, $pbActiveAndFinal = false, $pbWithDetail = true, $psLanguage = '')
  {
    if(!assert('is_key($pnCandidatePk)'))
      return new CDbResult();

    $sJoin = $sSelect = '';

    $sWhere = '';
    if($pbActiveOnly)
      $sWhere.= ' AND (spli.active = 1 AND spli.status <= 100 )';


    if($pbWithDetail)
    {
      $sSelect = ' spde.*, ';
      if(!empty($psLanguage))
      {
        $sJoin = ' AND spde.language = '.$this->oDB->dbEscapeString($psLanguage);
        $sJoin = 'INNER JOIN sl_position_detail as spde ON (spde.positionfk = spos.sl_positionpk '.$sJoin.') ';
      }
      else
        $sJoin = 'INNER JOIN sl_position_detail as spde ON (spde.positionfk = spos.sl_positionpk) ';
    }


    $sQuery = 'SELECT spli.*, spos.*, '.$sSelect.' scom.name as company_name, scom.sl_companypk, spli.status as current_status
      , spli.created_by as link_creator, spli.date_created as link_date
      FROM sl_position_link as spli
      INNER JOIN sl_position as spos ON (spos.sl_positionpk = spli.positionfk)
      INNER JOIN sl_company as scom ON (scom.sl_companypk = spos.companyfk) ';

    $sQuery.= $sJoin;

    $sQuery.= 'WHERE spli.candidatefk = '.$pnCandidatePk.' '.$sWhere.' ';

    /*if($pbActiveOnly)
      $sQuery.= 'ORDER BY spli.sl_position_linkpk DESC';
    else
      $sQuery.= 'ORDER BY spli.active DESC, spli.positionfk DESC, spli.date_expire DESC';*/
// update e gore siralamamiz istendi
      $sQuery.= "ORDER BY spli.sl_position_linkpk DESC";

    //echo $sQuery;
    return $this->oDB->executeQuery($sQuery);
  }



  public function getCompanyPosition($pnCompanyPk)
  {
    if(!assert('is_key($pnCompanyPk)'))
      return new CDbResult();

    //'sl_position', 'companyfk = '.$pnCompanyPk, '*', 'status DESC, date_created ASC')

    $sQuery = 'SELECT *
      FROM sl_position as spos
      INNER JOIN sl_position_detail as spde ON (spde.positionfk = spos.sl_positionpk)
      WHERE companyfk = '.$pnCompanyPk.'
      GROUP BY spde.positionfk
      ORDER BY spos.status DESC, spos.date_created DESC ';

    //echo $sQuery;
    return $this->oDB->executeQuery($sQuery);
  }

  public function getCompanyApplication($pnCompanyPk, $pbActiveOnly = false, $pbActiveAndFinal = false)
  {
    if(!assert('is_key($pnCompanyPk)'))
      return new CDbResult();

    $sWhere = $sJoin = $sSelect = '';

    if($pbActiveOnly)
      $sWhere = ' AND spli.active = 1 AND spli.status <= 100 '; //100 idi 101 yaptik
    elseif($pbActiveAndFinal)
      $sWhere = ' AND (spli.active = 1 AND (spli.status <= 100 OR spli.status >= 200))'; //100 idi 101 yaptik


    $sQuery = 'SELECT (case when app_comp.sl_companypk = '.$pnCompanyPk.' then 1 ELSE 0 END) as first_flag,
      (case when spos.companyfk = '.$pnCompanyPk.' then 1 ELSE 0 END) as second_flag,
      app_comp.sl_companypk as first_check, spos.companyfk as second_check, spli.*, spos.*,
      scom.name as company_name, scom.sl_companypk, spli.status as current_status,
      applicant.firstname, applicant.lastname
      FROM sl_position_link as spli
      INNER JOIN sl_position as spos ON (spos.sl_positionpk = spli.positionfk)
      INNER JOIN sl_company as scom ON (scom.sl_companypk = spos.companyfk)
      INNER JOIN sl_candidate as applicant ON (applicant.sl_candidatepk = spli.candidatefk)
      INNER JOIN sl_candidate_profile as app_pro ON (app_pro.candidatefk = applicant.sl_candidatepk)
      INNER JOIN sl_company as app_comp ON (app_comp.sl_companypk = app_pro.companyfk) ';

    $sQuery.= 'WHERE (app_comp.sl_companypk = '.$pnCompanyPk.' '.$sWhere.') OR (spos.companyfk = '.$pnCompanyPk.' AND spli.status <= 100 AND spli.active = 1 ) ';

    if($pbActiveOnly)
      $sQuery.= ' ORDER BY first_flag DESC,spli.sl_position_linkpk DESC ';
    else
      $sQuery.= ' ORDER BY first_flag DESC,spli.positionfk DESC, spli.date_expire DESC ';

    //ChromePhp::log($sQuery);
    //echo $sQuery;
    return $this->oDB->executeQuery($sQuery);
  }

  public function getPositionByPk($pnPositionPk)
  {
    if(!assert('is_key($pnPositionPk)'))
      return new CDbResult();

    return $this->getPosition($pnPositionPk);
  }

  public function getPosition($pvPositionPk = 0, $pvStatus = null, $pbOneDescription = true)
  {
    if(!assert('is_integer($pvPositionPk) OR is_arrayOfInt($pvPositionPk)'))
      return new CDbResult();

    if($pbOneDescription)
      $sQuery = 'SELECT DISTINCT(spos.sl_positionpk),';
    else
      $sQuery = 'SELECT ';

    $sQuery.= ' spos.*, spde.*, scom.name, scom.sl_companypk, sind.label as industry
      FROM sl_position as spos
      INNER JOIN sl_position_detail as spde ON (spde.positionfk = spos.sl_positionpk)
      INNER JOIN sl_company as scom ON (scom.sl_companypk = spos.companyfk)
      LEFT JOIN sl_industry as sind ON (sind.sl_industrypk = spos.industryfk)
      WHERE 1 ';

    if(!empty($pvPositionPk))
    {
      if(is_array($pvPositionPk))
        $sQuery.= ' AND spos.sl_positionpk IN ('.implode(',', $pvPositionPk).') ';
      else
        $sQuery.= ' AND spos.sl_positionpk = '.(int)$pvPositionPk.' ';
    }

    if($pvStatus === true)
      $sQuery.= ' AND spos.status = 1 ';
    elseif($pvStatus === false)
      $sQuery.= ' AND spos.status = 0 ';


      $sQuery.= ' ORDER BY spos.sl_positionpk DESC ';

    return $this->oDB->executeQuery($sQuery);
  }

  public function getPositionList($poQb = null, $pnLimit = 250,$afterAdd = false)
  {

    if($afterAdd != false)
    {
      $sQuery = "SELECT spos.*, spde.*, scom.name, scom.sl_companypk,
      spli.sl_position_linkpk, sind.label as industry,

      COUNT(DISTINCT(spli.candidatefk)) as nb_play,
      SUM(IF(spli.status < 101 AND spli.active = 1, 1, 0)) as nb_active
      FROM `sl_position` as spos
      INNER JOIN sl_position_detail as spde ON ((spde.positionfk = spos.sl_positionpk))
      INNER JOIN sl_company as scom ON ((scom.sl_companypk = spos.companyfk))
      LEFT JOIN sl_position_link as spli ON ((spli.positionfk = spos.sl_positionpk AND spli.active = 1))
      LEFT JOIN sl_industry as sind ON ((sind.sl_industrypk = spos.industryfk))
      where spos.sl_positionpk = ".$afterAdd."
      GROUP BY spos.sl_positionpk
      ORDER BY spos.sl_positionpk DESC LIMIT 0, 150";
    }
    else
    {
      if(empty($poQb))
        $poQb = $this->getQueryBuilder();

      $poQb->setTable('sl_position', 'spos');
      $poQb->addSelect('spos.*, spde.*, scom.name, scom.sl_companypk,
        spli.sl_position_linkpk, sind.label as industry,

        COUNT(DISTINCT(spli.candidatefk)) as nb_play,
        SUM(IF(spli.status < 101 AND spli.active = 1, 1, 0)) as nb_active');


      $poQb->addJoin('inner', 'sl_position_detail', 'spde', 'spde.positionfk = spos.sl_positionpk');
      $poQb->addJoin('inner', 'sl_company', 'scom', 'scom.sl_companypk = spos.companyfk');
      $poQb->addJoin('left', 'sl_position_link', 'spli', 'spli.positionfk = spos.sl_positionpk AND spli.active = 1');
      $poQb->addJoin('left', 'sl_industry', 'sind', 'sind.sl_industrypk = spos.industryfk');

      if($afterAdd != false)
      {
        $poQB->addWhere('spos.sl_positionpk = 8810');
      }

      $poQb->addGroup('spos.sl_positionpk');

      if(!$poQb->hasOrder())
        $poQb->addOrder('spos.sl_positionpk DESC');

      if($pnLimit > 0)
        $poQb->addLimit('0, '.$pnLimit);
      $sQuery = $poQb->getSql();
    }

    return $this->oDB->executeQuery($sQuery);
  }

  public function closePosition($position_id)
  {
    $sDate = date('Y-m-d H:i:s');
    $sQuery = " UPDATE sl_position_link SET active = '0', date_completed = '".$sDate."' WHERE positionfk = '".$position_id."' ";
    $this->oDB->executeQuery($sQuery);

    $sQuery = " SELECT DISTINCT(slp.candidatefk) as candidatefk FROM sl_position_link slp WHERE positionfk = '".$position_id."' ";
    $oDbResult = $this->oDB->executeQuery($sQuery);
    $candidates = $oDbResult->getAll();

    foreach ($candidates as $key => $candidate)
    {
      $candidate_id = $candidate['candidatefk'];

      $sQuery = "INSERT INTO sl_position_link (positionfk,candidatefk,date_created,created_by,status,in_play,comment,date_expire,active,date_completed)
      VALUES ('".$position_id."','".$candidate_id."','".$sDate."','-1','200','0','position_closed','".$sDate."','1','".$sDate."') ";

      $this->oDB->executeQuery($sQuery);
    }

    return "Position #".$position_id." closed & all candidates fallen off.";
  }

  public function update_date_completed($pre_record_id,$date_completed)
  {
    $sQuery = "UPDATE sl_position_link SET date_completed = '".$date_completed."' WHERE sl_position_linkpk = ".$pre_record_id;
    return $this->oDB->executeQuery($sQuery);
  }

  public function getPositionByLinkPk($pnLinkPk)
  {
    if(!assert('is_key($pnLinkPk)'))
      return new CDbResult();

    $sQuery = 'SELECT spos.*, spde.*, scom.name, scom.sl_companypk, spli.*, spos.status as pos_active
      , spcr.loginfk as credited, spcr.created_by as credited_by
      FROM sl_position_link as spli
      INNER JOIN sl_position as spos ON (spos.sl_positionpk = spli.positionfk)
      LEFT JOIN sl_position_credit as spcr ON (spcr.positionfk = spli.positionfk AND spcr.candidatefk = spli.candidatefk)

      INNER JOIN sl_position_detail as spde ON (spde.positionfk = spos.sl_positionpk)
      INNER JOIN sl_company as scom ON (scom.sl_companypk = spos.companyfk)
      WHERE spli.sl_position_linkpk = '.$pnLinkPk.' ';

    $oDbResult = $this->oDB->executeQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    if(!$bRead)
      return array();

    $asPosition = array();
    while($bRead)
    {
      if(empty($asPosition))
      {
        $asPosition = $oDbResult->getData();
        $asPosition['credited_user'] = $asPosition['credited_by'] = array();
      }

      if($oDbResult->getFieldValue('credited'))
      {
        $asPosition['credited_user'][] = (int)$oDbResult->getFieldValue('credited');
        $asPosition['credited_by'][] = (int)$oDbResult->getFieldValue('credited_by');
      }

      $bRead = $oDbResult->readNext();
    }

    return $asPosition;
  }


  public function getPositionApplicant($pnPositionPk, $pnExcludedPk = 0, $pnCandidatepk = 0, $pnMaxStatus = 0)
  {
    if(!assert('is_integer($pnPositionPk) && is_integer($pnExcludedPk) && is_integer($pnCandidatepk)'))
      return new CDbResult();

    $sQuery = 'SELECT spli.*, scan.*, scpr.*, scom.name as company_name
      ,spli.status as app_status, spli.date_created as app_date, spli.created_by as app_by
      FROM sl_position_link as spli

      INNER JOIN sl_candidate as scan ON (scan.sl_candidatepk = spli.candidatefk)
      LEFT JOIN sl_candidate_profile as scpr ON (scpr.candidatefk = scan.sl_candidatepk)
      LEFT JOIN sl_company as scom ON (scom.sl_companypk = scpr.companyfk)

      WHERE spli.active = 1 ';

    if(!empty($pnPositionPk))
      $sQuery.= ' AND  spli.positionfk = '.$pnPositionPk;

    if(!empty($pnExcludedPk))
      $sQuery.= ' AND spli.candidatefk <> '.$pnExcludedPk.' ';

    if(!empty($pnCandidatepk))
      $sQuery.= ' AND spli.candidatefk = '.$pnCandidatepk.' ';

    if(!empty($pnMaxStatus))
      $sQuery.= ' AND spli.status <= '.$pnMaxStatus.' ';


    $sQuery.= ' GROUP BY spli.candidatefk, spli.positionfk ';

    return $this->oDB->executeQuery($sQuery);
  }

  public function getExpiringPosition($psDate = '')
  {
    if(empty($psDate) || !is_date($psDate))
      $psDate = date('Y-m-d H:i:s');

    $sQuery = ' SELECT *, CONCAT(scan.lastname, \' \', scan.firstname) as candidate
      FROM sl_position_link as spli
      INNER JOIN sl_position as spos ON (spos.sl_positionpk = spli.positionfk)
      INNER JOIN sl_candidate as scan ON (scan.sl_candidatepk = spli.candidatefk)
      INNER JOIN shared_login as slog ON (slog.loginpk = spli.created_by)

      WHERE spli.active = 1 AND (spli.status < 101 OR spli.status = 150)  AND spli.date_expire <= "'.$psDate.'" ';

    //echo $sQuery.'<br />';

    return $this->executeQuery($sQuery);
  }





  public function getPlacement($filter = array(), $with_payment = false, $limit = 150)
  {
    $extra_select = $extra_query = '';
    $raw_revenue_data = $raw_data = $revenue_data = $prepared_data = array();

    $query = 'SELECT revenue.*, spde.positionfk, spde.title, sl_position.companyfk,';
    $query .= ' sl_company.name as company_name';
    $query .= ' FROM revenue';
    $query .= ' INNER JOIN sl_position_detail as spde ON (spde.positionfk = revenue.position)';
    $query .= ' INNER JOIN sl_position ON (sl_position.sl_positionpk = revenue.position)';
    $query .= ' INNER JOIN sl_company ON (sl_company.sl_companypk = sl_position.companyfk)';

    if (!empty($filter['revenue']))
        $query.= ' WHERE '.implode(' AND ', $filter['revenue']);

ChromePhp::log($query);

    $raw_revenue_data = $this->executeQuery($query);

    $read = $raw_revenue_data->readFirst();

    if (!$read)
      return false;

    while($read)
    {
      $raw_data = $raw_revenue_data->getData();

      $revenue_data[] = $raw_data;

      $read = $raw_revenue_data->readNext();
    }

    $i = 0;

    foreach ($revenue_data as $revenue)
    {
      $prepared_data[$revenue['id']]['id'] = $revenue['id'];
      $prepared_data[$revenue['id']]['closed_by'] = $revenue['closed_by'];
      $prepared_data[$revenue['id']]['status'] = $revenue['status'];
      $prepared_data[$revenue['id']]['date_paid'] = $revenue['date_paid'];
      $prepared_data[$revenue['id']]['date_signed'] = $revenue['date_signed'];
      $prepared_data[$revenue['id']]['date_start'] = $revenue['date_start'];
      $prepared_data[$revenue['id']]['date_due'] = $revenue['date_due'];
      $prepared_data[$revenue['id']]['candidate'] = $revenue['candidate'];
      $prepared_data[$revenue['id']]['salary'] = $revenue['salary'];
      $prepared_data[$revenue['id']]['salary_rate'] = $revenue['salary_rate'];
      $prepared_data[$revenue['id']]['amount'] = $revenue['amount'];
      $prepared_data[$revenue['id']]['position_id'] = $revenue['positionfk'];
      $prepared_data[$revenue['id']]['position_title'] = $revenue['title'];
      $prepared_data[$revenue['id']]['company_name'] = $revenue['company_name'];
      $prepared_data[$revenue['id']]['comment'] = $revenue['comment'];
      $prepared_data[$revenue['id']]['placement_count'] = $revenue['placement_count'];

      if ($revenue['candidate'] == 'retainer')
      {
        $query = 'SELECT * FROM revenue_member WHERE revenue_id = '.$revenue['id'];
      }
      else
      {
        if($with_payment)
        {
          $extra_select = ', revmem.* ';
          $extra_query = ' LEFT JOIN revenue_member as revmem ON (revmem.revenue_id = '.$revenue['id'].') ';
        }

        $query = 'SELECT CONCAT(scan.firstname, " ", scan.lastname) as candidate_name'.$extra_select;
        $query .= 'FROM sl_candidate as scan'.$extra_query;
        $query .= 'WHERE scan.sl_candidatepk = '.$revenue['candidate'];
      }

      $raw_placement_data = $this->executeQuery($query);
      $read = $raw_placement_data->readFirst();

      $test_array = array();

      while($read)
      {
        $raw_data = $raw_placement_data->getData();

        if ($revenue['candidate'] == 'retainer')
        {
          $prepared_data[$revenue['id']]['candidate_name'] = 'Retainer';
        }
        else
        {
          $prepared_data[$revenue['id']]['candidate_name'] = $raw_data['candidate_name'];
        }

        $prepared_data[$revenue['id']]['paid_users'][] = array('user' => $raw_data['loginpk'],
          'percentage' => $raw_data['percentage'], 'split_amount' => $raw_data['split_amount'],
          'user_position' => $raw_data['user_position']);

        $test_array[$raw_data['loginpk']] = '';

        $read = $raw_placement_data->readNext();
      }

      if (!empty($filter['member']))
      {
        if ($filter['member'] != $revenue['closed_by'])
        {
          if (!isset($test_array[$filter['member']]))
          {
            unset($prepared_data[$revenue['id']]);
            continue;
          }
        }
      }

      if ($limit > 0)
      {
        if ($i == $limit)
          break;

        $i += 1;
      }
    }

    uasort($prepared_data, sort_multi_array_by_value('date_signed', 'reverse'));

    return $prepared_data;
  }

  public function getPlacementOptions($pnPositionFk)
  {
    //Active or placed candidates
    $query = '
        SELECT DISTINCT(spli.candidatefk), spli.*,
        CONCAT(scan.firstname, " ", scan.lastname) as candidate,
        CONCAT(slog.firstname, " ", slog.lastname) as consultant,
        scan.created_by as creatorfk,
        spcr.loginfk as contributorfk,
        smee.created_by as meeting_creatorfk,
        smee.attendeefk as attendeefk

        FROM sl_position_link spli
        INNER JOIN sl_candidate as scan ON (scan.sl_candidatepk = spli.candidatefk)
        INNER JOIN shared_login as slog ON (slog.loginpk = spli.created_by)

        LEFT JOIN sl_position_credit as spcr ON (spcr.positionfk = spli.positionfk AND spcr.candidatefk = spli.candidatefk)
        LEFT JOIN sl_meeting as smee ON (smee.candidatefk = spli.candidatefk)

        WHERE spli.positionfk = '.$pnPositionFk.'
          AND
            (
              (
                spli.active = 1
                AND spli.status > 2
                AND spli.status < 200
              )
              OR
              (
                spli.active = 0
                AND spli.status >= 101
                AND spli.status < 150
              )
            )';

    //echo $sQuery.'<br />';
    return $this->executeQuery($query);
  }

  public function get_revenue_info($revenue_id)
  {
    $query = 'SELECT * FROM revenue WHERE id = '.$revenue_id;

    return $this->executeQuery($query);
  }

  public function get_revenue_members($revenue_id)
  {
    $query = 'SELECT * FROM revenue_member WHERE revenue_id = '.$revenue_id.' ORDER BY percentage DESC';

    return $this->executeQuery($query);
  }

  public function getCurrentStatus($pbActiveOnly = false)
  {
    $sQuery = ' SELECT DISTINCT(status) FROM sl_position_link ';

    if($pbActiveOnly)
       $sQuery.= ' WHERE active = 1 ';

    $sQuery.= ' ORDER BY status ';

    return $this->executeQuery($sQuery);
  }

  public function isCandidateInPlay($pnCandidatePk, $pnPositionFk = 0)
  {
    $sQuery = ' SELECT count(*) as nCount FROM sl_position_link
      WHERE active = 1 AND in_play = 1 AND candidatefk = '.$pnCandidatePk;

    if(!empty($pnPositionFk))
      $sQuery.= ' AND positionfk = '.$pnPositionFk;

    //dump($sQuery);
    $oDbResult = $this->executeQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    if(!$bRead)
      return 0;

    return (int)$oDbResult->getFieldValue('nCount');
  }

  public function getMaxActiveStatus($pnCandidatePk, $pnMaxStatus = 101, $psLimitDate = '')
  {
    $sQuery = ' SELECT MAX(status) as max_status FROM sl_position_link
      WHERE active = 1 AND status <= '.$pnMaxStatus.' AND candidatefk = '.$pnCandidatePk;

     if(!empty($psLimitDate))
      $sQuery.= ' AND date_created >= "'.$psLimitDate.'" ';

    //dump($sQuery);
    $oDbResult = $this->executeQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    if(!$bRead)
      return 0;

    return (int)$oDbResult->getFieldValue('max_status');
  }

  public function getLastInactiveStatus($pnCandidatePk, $pnMaxStatus = 250, $psLimitDate = '')
  {
    if(!assert('is_key($pnCandidatePk) && is_integer($pnMaxStatus)'))
      return 0;

    $sQuery = ' SELECT status  FROM sl_position_link
      WHERE active = 0 AND candidatefk = '.$pnCandidatePk.'
        AND status <= '.$pnMaxStatus.' ';

    if(!empty($psLimitDate))
      $sQuery.= ' AND date_created >= "'.$psLimitDate.'" ';

     $sQuery.= ' ORDER BY sl_position_linkpk DESC
     LIMIT 1 ';

    //dump($sQuery);
    $oDbResult = $this->executeQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    if(!$bRead)
      return 0;

    return (int)$oDbResult->getFieldValue('status');
  }

}
