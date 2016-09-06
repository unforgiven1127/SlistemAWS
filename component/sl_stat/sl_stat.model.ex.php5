<?php

class CSl_statModelEx extends CSl_statModel
{
  public function __construct()
  {
    parent::__construct();
    return true;
  }



  public function getSicChartNew($panUserPk, $psDateStart, $psDateEnd)
  {
    if(!assert('is_arrayOfInt($panUserPk)'))
      return array();


    $sQuery = 'SELECT count(*) as nCount, created_by, DATE_FORMAT(date_created, "%Y-%m") as sMonth
      FROM sl_candidate
      WHERE created_by IN ('.implode(',', $panUserPk).')
      AND date_created >= '.$this->oDB->dbEscapeString($psDateStart).' AND date_created < '.$this->oDB->dbEscapeString($psDateEnd).'

      GROUP BY sMonth, created_by
      ORDER BY sMonth ';

    //echo $sQuery;
    $asData = array();

    $oDbResult = $this->oDB->executeQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    while($bRead)
    {
      $asData[(int)$oDbResult->getFieldValue('created_by')][$oDbResult->getFieldValue('sMonth')] = (int)$oDbResult->getFieldValue('nCount');
      $bRead = $oDbResult->readNext();
    }

    return $asData;
  }

  public function getSicChartMet($panUserPk, $psDateStart, $psDateEnd, $group = 'researcher')
  {
    if(!assert('is_arrayOfInt($panUserPk)'))
      return array();

    $group_switch = 'created_by';

    if ($group == 'consultant')
      $group_switch = 'attendeefk';

    //no weight difference between phone and live meetings
    $sQuery = 'SELECT count(sl_meetingpk) as nCount, attendeefk, DATE_FORMAT(date_met, "%Y-%m") as sMonth, meeting_done
      FROM sl_meeting
      WHERE '.$group_switch.' IN ('.implode(',', $panUserPk).')
      AND date_met BETWEEN '.$this->oDB->dbEscapeString($psDateStart).' AND '.$this->oDB->dbEscapeString($psDateEnd).'

      GROUP BY attendeefk
      ORDER BY sMonth';

    $asData = array();

    $oDbResult = $this->oDB->executeQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    while($bRead)
    {
      $asData[(int)$oDbResult->getFieldValue('attendeefk')][$oDbResult->getFieldValue('sMonth')][(int)$oDbResult->getFieldValue('meeting_done')] = (int)$oDbResult->getFieldValue('nCount');
      $bRead = $oDbResult->readNext();
    }

    return $asData;
  }

  public function getSicChartPlay($panUserPk, $psDateStart, $psDateEnd)
  {
    if(!assert('is_arrayOfInt($panUserPk)'))
      return array();

    $sQuery = 'SELECT count(*) as nCount, created_by, DATE_FORMAT(date_created, "%Y-%m") as sMonth
      FROM sl_position_link
      WHERE created_by IN ('.implode(',', $panUserPk).')
      AND date_created >= '.$this->oDB->dbEscapeString($psDateStart).' AND date_created < '.$this->oDB->dbEscapeString($psDateEnd).'
      AND status >= 1 AND status < 150

      GROUP BY positionfk, candidatefk, created_by, sMonth
      ORDER BY sMonth ';

    //echo $sQuery;
    $asData = array();

    $oDbResult = $this->oDB->executeQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    while($bRead)
    {
      if(!isset($asData[(int)$oDbResult->getFieldValue('created_by')][$oDbResult->getFieldValue('sMonth')]))
        $asData[(int)$oDbResult->getFieldValue('created_by')][$oDbResult->getFieldValue('sMonth')] = 0;

      $asData[(int)$oDbResult->getFieldValue('created_by')][$oDbResult->getFieldValue('sMonth')]+= (int)$oDbResult->getFieldValue('nCount');
      $bRead = $oDbResult->readNext();
    }

    return $asData;
  }

  public function getSicChartPosition($panUserPk, $psDateStart, $psDateEnd)
  {
    if(!assert('is_arrayOfInt($panUserPk)'))
      return array();

    //Newly active positions, having their first CCM this month:
    // position created by me in the last 30 days (covers end of moth positions)
    // for which I've put a candidate in CCMX

    //select all the positions -30 days from start/end dates
    $sQuery = 'SELECT count(sl_positionpk) as nCount, spos.created_by, DATE_FORMAT(spos.date_created, "%Y-%m") as sMonth
      FROM sl_position as spos
      INNER JOIN sl_position_link as spli ON (spli.positionfk = spos.sl_positionpk
      AND spli.created_by = spos.created_by
      AND spli.date_created <= DATE_ADD(spos.date_created, INTERVAL 30 DAY)
      AND spli.status = 51)

      WHERE spos.created_by IN ('.implode(',', $panUserPk).')
        AND spos.date_created >= "'.date('Y-m-d', strtotime('-30 days', strtotime($psDateStart))).'"
        AND spos.date_created <= "'.$psDateEnd.'"

      GROUP BY spli.created_by, sMonth
      ORDER BY sMonth';

    //echo $sQuery;
    $asData = array();

    $oDbResult = $this->oDB->executeQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    while($bRead)
    {
      $asData[(int)$oDbResult->getFieldValue('created_by')][$oDbResult->getFieldValue('sMonth')] = (int)$oDbResult->getFieldValue('nCount');
      $bRead = $oDbResult->readNext();
    }
    return $asData;
  }

  public function getSicChartTarget($panUserPk)
  {
    if(!assert('is_arrayOfInt($panUserPk)'))
      return array();

    $sQuery = 'SELECT * FROM sl_stat_setting  WHERE loginfk IN ('.implode(',', $panUserPk).') ';

    //echo $sQuery;
    $asData = array();

    $oDbResult = $this->oDB->executeQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    while($bRead)
    {
      $asData[(int)$oDbResult->getFieldValue('loginfk')] = $oDbResult->getData();
      $bRead = $oDbResult->readNext();
    }

    return $asData;
  }


  public function getPiplelinePieData($panUserPk, $psDateStart, $psDateEnd, $pnStatus = 0, $pbTotal = true)
  {
    if(!assert('is_arrayOfInt($panUserPk)'))
      return array();

    if($pnStatus > 0)
      $sSqlStatus = ' AND status <= '.$pnStatus;
    else
      $sSqlStatus = '';

    if($pbTotal)
    {
      $sQuery = 'SELECT DISTINCT(CONCAT(candidatefk,"_", positionfk)), count(*) as nCount, status, DATE_FORMAT(spli.date_created, "%Y-%m") as sMonth, created_by
        FROM sl_position_link as spli
        WHERE created_by IN ('.implode(',', $panUserPk).')
        AND date_created >= '.$this->oDB->dbEscapeString($psDateStart).' AND date_created < '.$this->oDB->dbEscapeString($psDateEnd).'
        AND active = 1 '.$sSqlStatus;
    }
    else
    {
      $sQuery = 'SELECT MAX(sl_position_linkpk) as pk FROM sl_position_link
          WHERE created_by IN ('.implode(',', $panUserPk).')
          AND date_created >= '.$this->oDB->dbEscapeString($psDateStart).'
          AND date_created < '.$this->oDB->dbEscapeString($psDateEnd).' '.$sSqlStatus.'
          GROUP BY created_by, candidatefk, positionfk, DATE_FORMAT(date_created, "%Y-%m") ';

      $oDbResult = $this->oDB->executeQuery($sQuery);
      $bRead = $oDbResult->readFirst();
      if(!$bRead)
        return array();

      $sIds = '0';
      while($bRead)
      {
        $sIds.= ','.$oDbResult->getFieldValue('pk');
        $bRead = $oDbResult->readNext();
      }

      $sQuery = 'SELECT count(*) as nCount,
        status, DATE_FORMAT(spli.date_created, "%Y-%m") as sMonth, created_by
        FROM sl_position_link as spli
        WHERE created_by IN ('.implode(',', $panUserPk).')
        AND date_created >= '.$this->oDB->dbEscapeString($psDateStart).'
        AND date_created < '.$this->oDB->dbEscapeString($psDateEnd).'
        AND spli.sl_position_linkpk IN('.$sIds.')
        '.$sSqlStatus;
    }

    $sQuery.= ' GROUP BY created_by, candidatefk, positionfk, status, sMonth
      ORDER BY sMonth DESC ';

    //echo $sQuery;
    $asData = array();

    $oDbResult = $this->oDB->executeQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    while($bRead)
    {
      if(!isset($asData[$oDbResult->getFieldValue('sMonth')][(int)$oDbResult->getFieldValue('status')]))
        $asData[$oDbResult->getFieldValue('sMonth')][(int)$oDbResult->getFieldValue('status')] = 0;

      $asData[$oDbResult->getFieldValue('sMonth')][(int)$oDbResult->getFieldValue('status')]+= (int)$oDbResult->getFieldValue('nCount');
      $bRead = $oDbResult->readNext();
    }

    return $asData;
  }

  public function getPiplelineCandidate($panUserPk, $psDateStart, $psDateEnd, $pnMaxStatus = 100)
  {
    if(!assert('is_arrayOfInt($panUserPk)'))
      return array();

    //keep spli.* at the end to overwrite eventual created_by fields
    $sQuery = 'SELECT DISTINCT(candidatefk), spli.status as position_status, spde.title as position_title, scan.*,  spli.*,
      scan.created_by as candi_created
      FROM sl_position_link as spli
      INNER JOIN sl_position_detail as spde ON (spde.positionfk = spli.positionfk)
      INNER JOIN sl_candidate as scan ON (scan.sl_candidatepk = spli.candidatefk)

      WHERE spli.created_by IN ('.implode(',', $panUserPk).')
      AND spli.date_created >= '.$this->oDB->dbEscapeString($psDateStart).' AND spli.date_created < '.$this->oDB->dbEscapeString($psDateEnd).'
      AND spli.active = 1
      AND spli.status <= '.$pnMaxStatus.'
      ORDER BY spli.date_created ASC ';

    //echo $sQuery;
    $asData = array();

    $oDbResult = $this->oDB->executeQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    while($bRead)
    {
      $asData[] = $oDbResult->getData();
      $bRead = $oDbResult->readNext();
    }

    return $asData;
  }


  public function getPiplelineDetails($panUserPk, $psDateStart, $psDateEnd, $pnMaxStatus = 200)
  {
    if(!assert('is_arrayOfInt($panUserPk)'))
      return array();

    $sQuery = 'SELECT DISTINCT(CONCAT(candidatefk || "_" || spli.positionfk)), spli.*, spli.status as position_status, spde.title as position_title,
      scan.sex, scan.firstname, scan.lastname, scom.sl_companypk, scom.name as company_name
      FROM sl_position_link as spli
      INNER JOIN sl_position_detail as spde ON (spde.positionfk = spli.positionfk)
      INNER JOIN sl_candidate as scan ON (scan.sl_candidatepk = spli.candidatefk)

      INNER JOIN sl_position as spos ON (spos.sl_positionpk = spli.positionfk)
      INNER JOIN sl_company as scom ON (scom.sl_companypk = spos.companyfk)

      WHERE spli.created_by IN ('.implode(',', $panUserPk).')
      AND spli.date_created >= '.$this->oDB->dbEscapeString($psDateStart).' AND spli.date_created < '.$this->oDB->dbEscapeString($psDateEnd).'
      AND spli.status <= '.$pnMaxStatus.'
      ORDER BY spli.date_created DESC ';

    //echo $sQuery;
    $asCandidate = array();

    $oDbResult = $this->oDB->executeQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    while($bRead)
    {
      $asData = $oDbResult->getData();
      $asData['created_by'] = (int)$asData['created_by'];

      $sKey = $asData['positionfk'].'_'.$asData['candidatefk'];
      $sStatus = $asData['position_status'];


      $asCandidate[$asData['created_by']][$sKey]['data'] = $asData;

      if(!isset($asCandidate[$asData['created_by']][$sKey]['status'][$sStatus]) || $asCandidate[$asData['created_by']][$sKey]['status'][$sStatus] < $asData['date_created'])
        $asCandidate[$asData['created_by']][$sKey]['status'][$sStatus] = $asData['date_created'];

      $bRead = $oDbResult->readNext();
    }

    return $asCandidate;
  }

  public function getPiplelineDetailData($panUserPk, $psDateStart, $psDateEnd, $pnMaxStatus = 200)
  {
    if(!assert('is_arrayOfInt($panUserPk)'))
      return array();



     $sQuery = 'SELECT MAX(spli.sl_position_linkpk) as sl_position_linkpk
        FROM sl_position_link as spli

        WHERE spli.created_by IN ('.implode(',', $panUserPk).')
        AND spli.date_created >= '.$this->oDB->dbEscapeString($psDateStart).'
        AND spli.date_created < '.$this->oDB->dbEscapeString($psDateEnd).'
        AND spli.status <= '.$pnMaxStatus.'
        GROUP BY spli.positionfk, spli.candidatefk';

    //echo $sQuery;
    $anLinkPk = array();
    $oDbResult = $this->oDB->executeQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    while($bRead)
    {
      $anLinkPk[] = (int)$oDbResult->getFieldValue('sl_position_linkpk');
      $bRead = $oDbResult->readNext();
    }

    if(empty($anLinkPk))
      return array();

    $sQuery = 'SELECT spli.*, spli.status, spli.date_created, (UNIX_TIMESTAMP(spli.date_created) * 1000) as chartTime,
      scan.sex, scan.firstname, scan.lastname
      FROM sl_position_link as spli

      INNER JOIN sl_candidate as scan ON (scan.sl_candidatepk = spli.candidatefk)
      WHERE spli.sl_position_linkpk IN ('.implode(',', $anLinkPk).')

      GROUP BY spli.created_by, spli.positionfk, spli.candidatefk';

    //WHERE spli.sl_position_linkpk IN ('.implode(',', $anLinkPk).')

    //echo $sQuery;
    $asData = array();
    $oDbResult = $this->oDB->executeQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    while($bRead)
    {
      $asData[(int)$oDbResult->getFieldValue('created_by')][(int)$oDbResult->getFieldValue('status')][$oDbResult->getFieldValue('chartTime')] = $oDbResult->getData();
      $bRead = $oDbResult->readNext();
    }

    return $asData;
  }



  public function getPositionPipeData($panUserPk, $psDateStart, $psDateEnd, $pnMaxStatus = 200)
  {
    if(!assert('is_arrayOfInt($panUserPk)'))
      return array();

    $sQuery = 'SELECT spli.*, (UNIX_TIMESTAMP(spli.date_created) * 1000) as chartTime,
      MAX(spli.status) as status
      FROM sl_position_link as spli

      INNER JOIN sl_candidate as scan ON (scan.sl_candidatepk = spli.candidatefk)
      INNER JOIN sl_position_detail as spde ON (spde.positionfk = spli.positionfk)

      WHERE spli.date_created >= '.$this->oDB->dbEscapeString($psDateStart).'
      AND spli.date_created < '.$this->oDB->dbEscapeString($psDateEnd).'
      AND spli.status > 0 AND spli.status < 200
      AND spli.active = 1

      GROUP BY spli.positionfk
      ORDER BY spli.sl_position_linkpk ';

    //echo $sQuery;
    $asData = array();
    $oDbResult = $this->oDB->executeQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    while($bRead)
    {
      $asData[(int)$oDbResult->getFieldValue('status')][$oDbResult->getFieldValue('chartTime')] = $oDbResult->getData();
      $bRead = $oDbResult->readNext();
    }

    return $asData;
  }

  public function get_new_candidate_met($user_ids, $start_date, $end_date, $group = 'researcher')
  {
    $asData = array();

/*

SELECT m.sl_meetingpk, m.created_by, m.candidatefk, min(m2.sl_meetingpk)
FROM sl_meeting m
INNER JOIN sl_meeting m2 on m2.candidatefk = m.candidatefk
WHERE m.created_by = '332'
AND m.date_created > '2012-02-01 00:00:00'
AND m.date_created < '2016-02-30 00:00:00'
group by m.sl_meetingpk
order by m.candidatefk

 */
  $query = 'SELECT m.*, min(m2.sl_meetingpk) as min_date, slc._sys_status as candidate_status
        FROM sl_meeting m
        INNER JOIN sl_meeting m2 on m2.candidatefk = m.candidatefk and m2.meeting_done = 1
        INNER JOIN sl_candidate slc on slc.sl_candidatepk = m.candidatefk AND slc._sys_status = 0
        WHERE m.created_by IN ('.implode(',', $user_ids).')
        AND m.date_met >= "'.$start_date.'"
        AND m.date_met < "'.$end_date.'"
        group by m.sl_meetingpk
        order by m.candidatefk';

    $oDbResult = array();

    $oDbResult = $this->oDB->executeQuery($query);
    $read = $oDbResult->readFirst();

    while($read)
    {
      $temp = $oDbResult->getData();

      if(!isset($asData[$temp['created_by']]))
      {
        $asData[$temp['created_by']] = array();
      }

      if($temp['min_date'] == $temp['sl_meetingpk'] && $temp['meeting_done'] == 1)
      {
        array_push($asData[$temp['created_by']], $temp);

        //$asData[$temp['created_by']] = $temp;
      }
      $read = $oDbResult->readNext();
    }

    return $asData;
  }

  public function getKpiSetVsMet($user_ids, $start_date, $end_date, $group = 'researcher')
  {
    if(!assert('is_arrayOfInt($user_ids)'))
      return array();

    $group_switch = 'created_by';

    if ($group == 'consultant')
      $group_switch = 'attendeefk';

    $query = 'SELECT sl_meetingpk, candidatefk , created_by, date_created, date_met, attendeefk, meeting_done';
    $query .= ' FROM sl_meeting';
    $query .= ' WHERE meeting_done != -1';
    $query .= ' ORDER BY '.$group_switch;

//echo '<br><br>';
//var_dump($query);

    $data = array();
    $flip_user_ids = array_flip($user_ids);
    $meeting_array = $met_candidates_array = array();

    $db_result = $this->oDB->executeQuery($query);
    $read = $db_result->readFirst();
    while($read)
    {

      $temp = $db_result->getData();

      $meeting_array[] = $temp;

      if (!isset($met_candidates_array[$temp['candidatefk']]))
      {
        $met_candidates_array[$temp['candidatefk']]['times_met'] = 0;
        $met_candidates_array[$temp['candidatefk']]['oldest_meeting'] = date('Y-m-d');
      }

      if ((int)$temp['meeting_done'] > 0)
      {
        $met_candidates_array[$temp['candidatefk']]['times_met'] += 1;
        if (strtotime($met_candidates_array[$temp['candidatefk']]['oldest_meeting']) > strtotime($temp['date_created']))
          $met_candidates_array[$temp['candidatefk']]['oldest_meeting'] = $temp['date_created'];
      }

      $read = $db_result->readNext();
    }

    foreach ($meeting_array as $meeting)
    {

      $create_date = $meeting['date_created'];
      $month = date("m",strtotime($create_date));
      $year = date("Y",strtotime($create_date));

      $effectiveDate = date('Y-m-d', strtotime("+1 month", strtotime($create_date)));

      $new_month = date("m",strtotime($effectiveDate));
      $control_date = $year.'-'.$new_month.'-'.'06 00:00:00';

      $today = date("Y-m-d H:i:s");


      if (strtotime($meeting['date_created']) >= strtotime($start_date)
        && strtotime($meeting['date_created']) <= strtotime($end_date)
        && isset($flip_user_ids[$meeting[$group_switch]]))
      {
        if (!isset($data[$meeting[$group_switch]]))
        {
          $data[$meeting[$group_switch]] = array('set' => 0, 'met' => 0, 'set_meeting_info' => array(),
            'met_meeting_info' => array());
        }
        if($meeting['meeting_done'] == 0  && $meeting['date_updated'] == NULL && strtotime($today) >= strtotime($control_date ) )
        {
          # bir sonraki ayin 5 ini gecmis oluyor o nedenle cancel sayiyoruz
        }
        else
        {
          $data[$meeting[$group_switch]]['set'] += 1;
          $data[$meeting[$group_switch]]['set_meeting_info'][] = array('candidate' => $meeting['candidatefk'],
            'date' => $meeting['date_created']);
        }

      }

      if (strtotime($meeting['date_met']) >= strtotime($start_date)
        && strtotime($meeting['date_met']) <= strtotime($end_date)
        && isset($flip_user_ids[$meeting[$group_switch]]))
      {
        if (!isset($data[$meeting[$group_switch]]))
        {
          $data[$meeting[$group_switch]] = array('set' => 0, 'met' => 0, 'set_meeting_info' => array(),
            'met_meeting_info' => array());
        }

        $temp_validation_date = date('Y-m', strtotime($met_candidates_array[$meeting['candidatefk']]['oldest_meeting']));

        if($meeting['candidatefk'] == '457' && $meeting['candidatefk'] == '319306')
        {
          ChromePhp::log($met_candidates_array[$meeting['candidatefk']]['times_met']);
          ChromePhp::log($temp_validation_date);
          ChromePhp::log($temp_validation_date);
        }

        if ((int)$meeting['meeting_done'] > 0
          && ($met_candidates_array[$meeting['candidatefk']]['times_met'] <= 1 ||
          ($temp_validation_date >= date('Y-m', strtotime($start_date)) &&
            $temp_validation_date <= date('Y-m', strtotime($end_date))) ))
        {
          $data[$meeting[$group_switch]]['met'] += 1;
          $data[$meeting[$group_switch]]['met_meeting_info'][] = array('candidate' => $meeting['candidatefk'],
            'date' => $meeting['date_met']);

          $met_candidates_array[$meeting['candidatefk']]['oldest_meeting'] = '1950-05-05';
        }
      }
    }

    return $data;
  }

  public function getKpiInPlay($panUserPk, $psDateStart, $psDateEnd)
  {
    if(!assert('is_arrayOfInt($panUserPk)'))
      return array();

    //no weight difference between phone and live meetings
    /*$sQuery = 'SELECT count(*) as nCount, spli.created_by
      FROM sl_position_link as spli
      WHERE spli.created_by IN ('.implode(',', $panUserPk).')
      AND spli.status > 0
      AND spli.status < 150
      AND spli.active = 1
      AND spli.in_play = 1

      AND spli.date_created >= "'.$psDateStart.'"
      AND spli.date_created < "'.$psDateEnd.'"

      GROUP BY created_by
      ORDER BY nCount DESC ';*/


    $sQuery = 'SELECT count(*) as nCount, in_play.created_by
      FROM
      (
        SELECT * FROM sl_position_link as spli
        WHERE spli.created_by IN ('.implode(',', $panUserPk).')
        AND spli.status > 0
        AND spli.status < 150
        AND spli.in_play = 1

        AND spli.date_created >= "'.$psDateStart.'"
        AND spli.date_created < "'.$psDateEnd.'"
        GROUP BY spli.positionfk, spli.candidatefk
        ) as in_play

      GROUP BY in_play.created_by
      ORDER BY nCount DESC ';

    //echo $sQuery;
    $asData = array();

    $oDbResult = $this->oDB->executeQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    while($bRead)
    {
      $asData[(int)$oDbResult->getFieldValue('created_by')] = (int)$oDbResult->getFieldValue('nCount');
      $bRead = $oDbResult->readNext();
    }

    return $asData;
  }




  /**
   * Revenue board
   *
   * @param type $anUser
   * @param type $sDateStart
   * @param type $sDateEnd
   * @return array
   */
  public function getPlacementData($panUserPk, $psDateStart, $psDateEnd, $psLocation = 'all', $psGroupBy = '')
  {
    /*if(!assert('is_arrayOfInt($panUserPk)'))
      return array();*/

    switch($psLocation)
    {
      case 'all': $sLocationSql = '';  break;

      default:
        $sLocationSql = ' AND location = "'.substr(strtolower($psLocation), 0, 3).'" ';
        break;
    }



    if(empty($psGroupBy) || $psGroupBy == 'user')
    {
      $sGroup = ' GROUP BY sppa.loginfk ';
    }
    elseif($psGroupBy == 'location')
    {
      $sGroup = ' GROUP BY spla.location ';
    }
    elseif($psGroupBy == 'team')
    {
      $sGroup = ' GROUP BY lgme.login_groupfk ';
    }


    //sppa.loginfk IN ('.implode(',', $panUserPk).')

    $sQuery = 'SELECT DISTINCT(sppa.sl_placement_paymentpk), sppa.*, SUM(sppa.amount) as revenue_signed,
      SUM(IF(spla.date_paid, sppa.amount, 0)) as revenue_paid, spla.location,
      SUM(sppa.placed) as revenue_placed,
      lgme.login_groupfk as groupfk

      FROM sl_placement_payment as sppa
      INNER JOIN sl_placement as spla ON(spla.sl_placementpk = sppa.placementfk)
      LEFT JOIN login_group_member as lgme ON (lgme.loginfk = sppa.loginfk AND lgme.login_groupfk < 100 )

      WHERE  spla.date_signed >= "'.$psDateStart.'"
      AND spla.date_signed < "'.$psDateEnd.'"
       '.$sLocationSql.'
       '.$sGroup.'
      ORDER BY revenue_signed DESC ';

    //echo $sQuery;
    $asData = array();

    $oDbResult = $this->oDB->executeQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    while($bRead)
    {
      $asData[(int)$oDbResult->getFieldValue('loginfk')] = $oDbResult->getData();
      $bRead = $oDbResult->readNext();
    }

    return $asData;
  }
  /**
   * Revenue board
   *
   * @param type $anUser
   * @param type $sDateStart
   * @param type $sDateEnd
   * @return array
   */
  public function getContributorData($panUserPk, $psDateStart, $psDateEnd, $psLocation = 'all')
  {
    /*if(!assert('is_arrayOfInt($panUserPk)'))
      return array();*/

    switch($psLocation)
    {
      case 'all': $sLocationSql = '';  break;

      default:
        $sLocationSql = ' AND location = "'.substr(strtolower($psLocation), 0, 3).'" ';
        break;
    }

    //Get all the [ positionfk, candidatefk ] active during the requested period
    $sQuery = 'SELECT positionfk, candidatefk
      FROM sl_position_link as spli
      WHERE spli.date_created >= "'.$psDateStart.'" AND spli.date_created < "'.$psDateEnd.'"
      GROUP BY positionfk, candidatefk';

    //dump($sQuery);

    $oDbResult = $this->oDB->executeQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    if(!$bRead)
      return array();

    $asLink = array();
    while($bRead)
    {
      //dump('pos: '.$oDbResult->getFieldValue('positionfk').' - candi '.$oDbResult->getFieldValue('candidatefk'));
      $asLink[] = '(spcr.positionfk = '.$oDbResult->getFieldValue('positionfk').' AND spcr.candidatefk = '.$oDbResult->getFieldValue('candidatefk').')';
      $bRead = $oDbResult->readNext();
    }

    //get the contributors for each [ positionfk, candidatefk ] found above
    $sQuery = 'SELECT spcr.*, MAX(spli.status) as status

      FROM sl_position_credit as spcr
      INNER JOIN sl_position_link as spli ON (spli.positionfk = spcr.positionfk AND spli.candidatefk = spcr.candidatefk)

      WHERE spcr.loginfk IN ('.implode(',', $panUserPk).')
      AND( '.implode(' OR ',$asLink).' )
        AND spli.status < 150

      GROUP BY spcr.positionfk, spcr.candidatefk, spcr.loginfk
      ';

    //spcr.loginfk IN ('.implode(',', $panUserPk).')
    //dump(implode(',', $panUserPk));

    //dump($sQuery);
    $oDbResult = $this->oDB->executeQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    if(!$bRead)
      return array();

    $asData = array();
    while($bRead)
    {
      $asContib = $oDbResult->getData();
      //dump($asContib);

      if((int)$asContib['status'] == 101)
      {
        $nPlacement = 1;
        $nActive = 0;
      }
      else
      {
        $nPlacement = 0;
        $nActive = 1;
      }

      set_array($asData[(int)$asContib['loginfk']]['active'], 0);
      set_array($asData[(int)$asContib['loginfk']]['placement'], 0);

     $asData[(int)$asContib['loginfk']]['active']+= $nActive;
     $asData[(int)$asContib['loginfk']]['placement']+= $nPlacement;

      $bRead = $oDbResult->readNext();
    }

    return $asData;
  }


  public function getAnalystCandidatesSummary($panUserPk, $psDateStart, $psDateEnd)
  {
    if(!assert('is_arrayOfInt($panUserPk)'))
      return array();

    //no weight difference between phone and live meetings
    $sQuery = 'SELECT * FROM sl_candidate
      WHERE date_created >= "'.$psDateStart.'"
        AND date_created < "'.$psDateEnd.'"
        ORDER BY date_created ';

    //echo $sQuery;
    $asData = array();

    $oDbResult = $this->oDB->executeQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    while($bRead)
    {
      $asData[(int)$oDbResult->getFieldValue('loginfk')] = $oDbResult->getData();
      $bRead = $oDbResult->readNext();
    }

    return $asData;
  }
  public function getMeetings($panUserPk, $psDateStart, $psDateEnd)
  {
    if(!assert('is_arrayOfInt($panUserPk)'))
      return array();

    //no weight difference between phone and live meetings
    $sQuery = 'SELECT * FROM sl_meeting as smee
      INNER JOIN sl_candidate as scan ON (scan.sl_candidatepk = smee.candidatefk)
      WHERE date_meeting >= "'.$psDateStart.'"
        AND date_meeting < "'.$psDateEnd.'"
        AND  (smee.created_by IN ('.implode(',', $panUserPk).') OR  smee.attendeefk IN ('.implode(',', $panUserPk).') )
        ORDER BY date_meeting ';

    //echo $sQuery;
    $asData = array();

    $oDbResult = $this->oDB->executeQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    while($bRead)
    {
      $asData[(int)$oDbResult->getFieldValue('loginfk')] = $oDbResult->getData();
      $bRead = $oDbResult->readNext();
    }

    return $asData;
  }


  public function getNewCandidates($panUserPk, $psDateStart, $psDateEnd)
  {
    if(!assert('is_arrayOfInt($panUserPk)'))
      return array();

    //no weight difference between phone and live meetings
    $sQuery = 'SELECT * FROM  sl_candidate as scan
      LEFT JOIN sl_candidate_profile as scpr ON (scpr.candidatefk = scan.sl_candidatepk)
      WHERE scan.date_created >= "'.$psDateStart.'"
      AND scan.date_created < "'.$psDateEnd.'"
      AND scan.created_by IN ('.implode(',', $panUserPk).')
      ORDER BY scan.date_created ';

    //echo $sQuery;
    $asData = array();

    $oDbResult = $this->oDB->executeQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    while($bRead)
    {
      $asData[(int)$oDbResult->getFieldValue('sl_candidatepk')] = $oDbResult->getData();
      $bRead = $oDbResult->readNext();
    }

    return $asData;
  }

  public function getAnalystPieData($panUserPk, $psDateStart, $psDateEnd, $pnStatus = 0)
  {
    if(!assert('is_arrayOfInt($panUserPk)'))
      return array();

    if($pnStatus > 0)
      $sSqlStatus = ' AND status <= '.$pnStatus;
    else
      $sSqlStatus = '';

    $sIds = implode(',', $panUserPk);

    $sQuery = 'SELECT DISTINCT(CONCAT(spli.candidatefk,"_", spli.positionfk)), count(*) as nCount, spli.status,
      DATE_FORMAT(spli.date_created, "%Y-%m") as sMonth, spli.created_by
      FROM sl_position_link as spli
      INNER JOIN sl_candidate as scan ON (scan.sl_candidatepk = spli.candidatefk)
      WHERE
      (spli.created_by IN ('.$sIds.') OR scan.created_by IN ('.$sIds.') )
      AND spli.date_created >= '.$this->oDB->dbEscapeString($psDateStart).'
      AND spli.date_created < '.$this->oDB->dbEscapeString($psDateEnd).'
      AND spli.active = 1 '.$sSqlStatus;

    $sQuery.= ' GROUP BY created_by, candidatefk, positionfk, status, sMonth
      ORDER BY sMonth DESC ';

    $asData = array();

    $oDbResult = $this->oDB->executeQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    while($bRead)
    {
      if(!isset($asData[$oDbResult->getFieldValue('sMonth')][(int)$oDbResult->getFieldValue('status')]))
        $asData[$oDbResult->getFieldValue('sMonth')][(int)$oDbResult->getFieldValue('status')] = 0;

      $asData[$oDbResult->getFieldValue('sMonth')][(int)$oDbResult->getFieldValue('status')]+= (int)$oDbResult->getFieldValue('nCount');
      $bRead = $oDbResult->readNext();
    }

    return $asData;
  }

  public function get_revenue_data($request_date = '', $location = '')
  {

    $revenue_data = $revenue_data_raw = array();

    if (empty($request_date))
      $request_date = date('Y');

    $date_start = $request_date.'-01-01';
    $date_end = $request_date.'-12-31';

    $query = 'SELECT id, amount, location, status, refund_amount, currency, candidate ';
    $query .= 'FROM revenue ';
    $query .= 'WHERE date_due BETWEEN "'.$date_start.'" AND "'.$date_end.'"';

    if (!empty($location))
      $query = ' AND location = "'.$location.'"';

    $db_result = $this->oDB->executeQuery($query);
    $read = $db_result->readFirst();
    if ($read)
    {
      while($read)
      {
        $row = $db_result->getData();
        $revenue_data_raw[$row['id']] = $row;

        $read = $db_result->readNext();
      }

      $array_for_printing = $revenue_data_raw;
      $clear_data = $revenue_data_raw;

      $year = date("Y");
      $ccm1_start_date = $year."-01-01 00:00:00";
      $ccm1_end_date = $year."-12-31 23:59:59";

      $query = "SELECT l.*, sln.shortname as nationality
                FROM login l
                LEFT JOIN sl_position_link s ON s.created_by = l.loginpk  AND active = 0 AND date_completed BETWEEN '".$ccm1_start_date."' AND '".$ccm1_end_date."'
                LEFT JOIN sl_nationality sln ON l.nationalityfk = sln.sl_nationalitypk
                WHERE (l.position = 'Researcher' OR l.loginpk = '457' OR l.loginpk = '481' OR l.loginpk = '466') AND l.loginpk != '382'"; // saruul un hem consultant hem researcher da gorunebilmesi icin...

      $db_result = $this->oDB->executeQuery($query);
      $read = $db_result->readFirst();
//var_dump($query);// 382
//var_dump('<br><br>');


      $revenue_data['Consultant']['former'] = array('name' => 'Former', 'nationality' => 0, 'do_not_count_placed' => array(), 'total_amount' => 0,
        'placed' => 0, 'paid' => 0, 'signed' => 0, 'team' => 'Not defined', 'userPosition' => 'Not defined');

      $revenue_data['Researcher']['former'] = array('name' => 'Former', 'nationality' => 0, 'do_not_count_placed' => array(), 'total_amount' => 0,
        'placedRevenue' => 0, 'paid' => 0, 'signed' => 0, 'team' => 'Not defined', 'userPosition' => 'Not defined');


      while($read)
      {
        $row = $db_result->getData();

        $user_id = $row['loginpk'];

        if($user_id == '457' || $user_id == '481' || $user_id == '466')
        {// saruul un hem consultant hem researcher da gorunebilmesi icin...
          $row['position'] = "Researcher";
        }

        $users = array($user_id);
        //array_push($users,$user_id);
        $ccms = $this->get_ccm_data($users, $ccm1_start_date, $ccm1_end_date, $group = 'researcher');

        $ccm1_count = (int)$ccms[$user_id]['ccm1_done'];
        $mccm_count = (int)$ccms[$user_id]['ccm2_done'] + (int)$ccms['researcher'][$user_id]['mccm_done'];
        $placed_count = (int)$ccms[$user_id]['placedRevenue'];

        //var_dump($user_id);

        if (!$row['status'] || $row['revenue_chart_flag'] == "p")
        {
          $user_id = 'former';
        }

        if (empty($revenue_data['Researcher'][$user_id][$row['position']]['name']))
            $revenue_data['Researcher'][$user_id][$row['position']]['name'] = substr($row['firstname'], 0, 1).'. '.$row['lastname'];
        if (empty($revenue_data['Researcher'][$user_id][$row['position']]['position']))
          $revenue_data['Researcher'][$user_id][$row['position']]['userPosition'] = $row['position'];
        if (empty($revenue_data['Researcher'][$user_id][$row['position']]['nationality']))
              $revenue_data['Researcher'][$user_id][$row['position']]['nationality'] = $row['nationality'];

        if(empty($revenue_data['Researcher'][$user_id][$row['position']]['placedRevenue']))
        {
          if($placed_count == null)
          {
            $placed_count = 0;
          }
          $revenue_data['Researcher'][$user_id][$row['position']]['placedRevenue'] = $placed_count;
        }
//echo $revenue_data[$user_id][$row['position']]['name'].' - ';
//var_dump($revenue_data[$user_id][$row['position']]['placedRevenue']);
//echo "<br><br>";
        if(empty($revenue_data['Researcher'][$user_id][$row['position']]['ccm1']))
        {
          if($ccm1_count == null)
          {
            $ccm1_count = 0;
          }
          $revenue_data['Researcher'][$user_id][$row['position']]['ccm1'] = $ccm1_count;
        }
        if(empty($revenue_data['Researcher'][$user_id][$row['position']]['mccm']))
        {
          if($mccm_count == null)
          {
            $mccm_count = 0;
          }
          $revenue_data['Researcher'][$user_id][$row['position']]['mccm'] = $mccm_count;
        }

        if(empty($revenue_data['Researcher'][$user_id]['sort']))
        {

          $calculate_sort = ($placed_count * 100000) + ($mccm_count * 1000) + ($ccm1_count * 10);
          $revenue_data['Researcher'][$user_id]['sort'] = $calculate_sort;

        }

        $read = $db_result->readNext();
      }
//var_dump($revenue_data);
// Researcher position will be included MCA
//LEFT JOIN login ON revenue_member.loginpk = login.loginpk => AND (login.position LIKE "Consultant" OR login.position LIKE "Researcher")
      $query = 'SELECT revenue_member.*,login.position as userPosition, login.id, login.firstname, login.lastname, login.status, sl_nationality.shortname AS nationality, login.revenue_chart_flag ';
      $query .= 'FROM revenue_member ';
      $query .= 'LEFT JOIN login ON revenue_member.loginpk = login.loginpk ';
      $query .= 'LEFT JOIN sl_nationality ON login.nationalityfk = sl_nationality.sl_nationalitypk';

//var_dump($query);
//exit;
      $db_result = $this->oDB->executeQuery($query);
      $read = $db_result->readFirst();


      $flag = 0;
      while($read)
      {
        $row = $db_result->getData();

        if ($row['id'] == 'bizreach' || $row['id'] == 'othercollab' || empty($row['id']))
        {
          $read = $db_result->readNext();
          continue;
        }

        $array_for_printing[$row['revenue_id']]['members'][$row['loginpk']] = $row;

        if (isset($revenue_data_raw[$row['revenue_id']]))
        {
          $current_revenue_info = $revenue_data_raw[$row['revenue_id']];

          if (!$row['status'] || $row['revenue_chart_flag'] == "p")
          {
            $user_id = 'former';
            //$row['user_position'] = 'Consultant';
            //if (empty($revenue_data[$row['user_position']][$row['userPosition']][$user_id]['placed']))
            if($flag == 0)
            {
              $revenue_data[$row['user_position']][$user_id][$row['userPosition']]['placed'] = 0;
              $flag = 1;
            }

            if ($row['user_position'] == 'Consultant' && !isset($revenue_data[$row['user_position']][$user_id][$row['userPosition']]['do_not_count_placed'][$row['loginpk']]))
            {
              $temp_placed = $this->get_placement_number_revenue(array($row['loginpk']), $date_start, $date_end);
              $revenue_data[$row['user_position']][$user_id][$row['userPosition']]['placed'] += $temp_placed[$row['loginpk']]['placed'];
              $revenue_data[$row['user_position']][$user_id][$row['userPosition']]['candidates'] .= ';'.$clear_data[$row['revenue_id']]['candidate'];
            }

            else if ($row['user_position'] == 'Researcher' && !isset($revenue_data[$row['user_position']][$user_id][$row['userPosition']]['do_not_count_placed'][$row['loginpk']]))
            {
              $temp_placed = $this->get_placement_number_revenue(array($row['loginpk']), $date_start, $date_end);
              $revenue_data[$row['user_position']][$user_id][$row['userPosition']]['placedRevenue'] += $temp_placed[$row['loginpk']]['placed'];
              $revenue_data[$row['user_position']][$user_id][$row['userPosition']]['candidates'] .= ';'.$clear_data[$row['revenue_id']]['candidate'];
            }

            $revenue_data[$row['user_position']][$user_id][$row['userPosition']]['name'] = "Former";
            $revenue_data[$row['user_position']][$user_id][$row['userPosition']]['do_not_count_placed'][$row['loginpk']] = '';

            //echo'<br><br>';
            //var_dump($revenue_data);
            //echo'<br><br>';
          }
          else
          {
            $user_id = $row['loginpk'];

            if (empty($revenue_data[$row['user_position']][$user_id][$row['user_position']]['placed']))
              $revenue_data[$row['user_position']][$user_id][$row['user_position']]['placed'] = 0;

            if (empty($revenue_data[$row['user_position']][$user_id][$row['user_position']]['nationality']))
              $revenue_data[$row['user_position']][$user_id][$row['user_position']]['nationality'] = $row['nationality'];

            if (empty($revenue_data[$row['user_position']][$user_id][$row['user_position']]['userPosition']))
              $revenue_data[$row['user_position']][$user_id][$row['user_position']]['userPosition'] = $row['userPosition'];

            if (empty($revenue_data[$row['user_position']][$user_id][$row['user_position']]['placed']))
            {
              $temp_placed = $this->get_placement_number_revenue(array($user_id), $date_start, $date_end);
              $revenue_data[$row['user_position']][$user_id][$row['user_position']]['placed'] += $temp_placed[$user_id]['placed'];
              $revenue_data[$row['user_position']][$user_id][$row['user_position']]['candidates'] .= ';'.$clear_data[$row['revenue_id']]['candidate'];
            }

            if (empty($revenue_data[$row['user_position']][$user_id][$row['user_position']]['name']))
                $revenue_data[$row['user_position']][$user_id][$row['user_position']]['name'] = substr($row['firstname'], 0, 1).'. '.$row['lastname'];

          }
          if (!isset($revenue_data[$row['user_position']][$user_id][$row['user_position']]['paid']))
            $revenue_data[$row['user_position']][$user_id][$row['user_position']]['paid'] = $revenue_data[$row['user_position']][$user_id][$row['user_position']]['signed'] = $revenue_data[$row['user_position']][$user_id][$row['user_position']]['total_amount'] = 0;

          if (empty($revenue_data[$row['user_position']][$user_id]['team']))
            $revenue_data[$row['user_position']][$user_id]['team'] = $this->get_user_team($user_id);

          if (strtolower($row['user_position']) == 'consultant')
          //if(1) // did not calculate researchers so changed to 1
          {
            switch ($current_revenue_info['status'])
            {
              case 'paid':
              case 'refund':
              case 'retainer':
                $revenue_data[$row['user_position']][$user_id]['consultant']['paid'] += ($current_revenue_info['amount'] - $current_revenue_info['refund_amount']) * ($row['percentage'] / 100);
                break;
            }

            $revenue_data[$row['user_position']][$user_id]['consultant']['signed'] += $current_revenue_info['amount'] * ($row['percentage'] / 100);

            $revenue_data[$row['user_position']][$user_id]['sort'] += $revenue_data[$row['user_position']][$user_id]['consultant']['signed']*10000000;

            if ($row['status'])
            {
              $revenue_data[$row['user_position']][$user_id]['total_amount'] += ($current_revenue_info['amount'] - $current_revenue_info['refund_amount']) * ($row['percentage'] / 100);

            }
          }

          if (strtolower($row['user_position']) == 'researcher')
          //if(1) // did not calculate researchers so changed to 1
          {
            switch ($current_revenue_info['status'])
            {
              case 'paid':
              case 'refund':
              case 'retainer':
                $revenue_data[$row['user_position']][$user_id]['researcher']['paid'] += ($current_revenue_info['amount'] - $current_revenue_info['refund_amount']) * ($row['percentage'] / 100);
                break;
            }

            $revenue_data[$row['user_position']][$user_id]['researcher']['signed'] += $current_revenue_info['amount'] * ($row['percentage'] / 100);

            $revenue_data[$row['user_position']][$user_id]['sort'] += $revenue_data[$row['user_position']][$user_id]['researcher']['signed']*10000000;

            if ($row['status'])
            {
              $revenue_data[$row['user_position']][$user_id]['total_amount'] += ($current_revenue_info['amount'] - $current_revenue_info['refund_amount']) * ($row['percentage'] / 100);

            }
            /*else
            {
              $revenue_data[$row['user_position']][$user_id]['sort'] = $revenue_data['Researcher'][$user_id][$row['position']]['placedRevenue'];
            }*/
          }
        }
        $read = $db_result->readNext();
      }

      $revenue_data['Researcher']['former']['sort'] = -1000000; // siralamada en altta olmasi icin

      foreach ($revenue_data['Consultant'] as $key => $value)
      {
        $revenue_data['Consultant'][$key]['sort'] = $revenue_data['Consultant'][$key]['total_amount'];
      }
      $revenue_data['Consultant']['former']['sort'] = -1000000; // siralamada en altta olmasi icin

      uasort($revenue_data['Consultant'], sort_multi_array_by_value('sort', 'reverse'));
      uasort($revenue_data['Researcher'], sort_multi_array_by_value('sort', 'reverse'));

      /*foreach ($revenue_data['Researcher'] as $key => $value)
      {
        if($key == 'former')
        {
          echo $key." - ";
          var_dump($value);
          echo "<br><br>";
        }
      }

      foreach ($revenue_data['Consultant'] as $key => $value)
      {
        if($key == 'former')
        {
          echo $key." - ";
          var_dump($value);
          echo "<br><br>";
        }
      }*/

    }

    //exit;
    //echo "<br><br>";
    //var_dump($revenue_data);//
    return $revenue_data;
  }

  public function get_placement_number_revenue($user_ids, $date_start = '', $date_end = '')
  {
    $placements = array();

    if (empty($date_start))
      $date_start = date('Y').'-01-01';

    if (empty($date_end))
      $date_end = date('Y').'-12-31';

    $query = 'SELECT position, candidate, closed_by';
    $query .= ' FROM revenue';
    $query .= ' WHERE closed_by IN ('.implode(',', $user_ids).') AND placement_count = "yes"';
    $query .= ' AND date_due BETWEEN "'.$date_start.'" AND "'.$date_end.'"';
    $query .= ' ORDER BY closed_by';

    $db_result = $this->oDB->executeQuery($query);
    $read = $db_result->readFirst();

    while ($read)
    {
      $row = $db_result->getData();

      $placements[$row['closed_by']]['candidates'][$row['candidate']] = $row['candidate'];

      $read = $db_result->readNext();
    }

    foreach ($user_ids as $value)
    {
      if (!empty($placements[$value]))
        $placements[$value]['placed'] = count($placements[$value]['candidates']);
      else
      {
        $placements[$value]['placed'] = 0;
        $placements[$value]['candidates'] = array();
      }
    }

    return $placements;
  }

  private function get_user_team($user_id)
  {
    $group = 'Not defined';
    $raw_info = array();
    if ($user_id != 'former')
    {
      $query = 'SELECT login_group_member.login_groupfk, login_group.title ';
      $query .= 'FROM login_group_member ';
      $query .= 'LEFT JOIN login_group ON login_group_member.login_groupfk = login_group.login_grouppk ';
      $query .= 'WHERE login_group_member.loginfk = "'.$user_id.'"';

      $db_result = $this->oDB->executeQuery($query);
      $read = $db_result->readFirst();

      while ($read)
      {
        $row = $db_result->getData();

        if ($row['login_groupfk'] >= 1 && $row['login_groupfk'] <= 10)
        {
          $group = $row['title'];
          break;
        }

        $read = $db_result->readNext();
      }
    }

    return $group;
  }

  public function get_ccm_data($user_ids, $start_date, $end_date, $group = 'researcher')
  {
    //var_dump($user_ids);
    $ccm_data = $repeating_info = $ccm_keys = array();

    $start_date_stamp = strtotime($start_date);
    $end_date_stamp = strtotime($end_date);

    if ($group == 'consultant')
    {
      $query = 'SELECT slp.sl_position_linkpk, slp.positionfk, slp.candidatefk, slp.created_by, slp.status, slp.date_completed, slp.date_created as ccm_create_date, slp.active, slc._sys_status as candidate_status';
      $query .= ' FROM sl_position_link slp';
      $query .= ' INNER JOIN sl_candidate slc on slc.sl_candidatepk = slp.candidatefk';
      $query .= ' WHERE slp.created_by IN ('.implode(',', $user_ids).')';
      $query .= ' AND status >= 51';
                  //AND date_created >= "'.$start_date.'"
                  //AND date_created <= "'.$end_date.'"';
    }

    else if ($group == 'researcher')
    {
      $query = 'SELECT min(m2.sl_meetingpk) as min_date,slm.sl_meetingpk,m2.meeting_done as m2_meeting_done,slm.meeting_done,slm.created_by as meeting_created_by, slp.sl_position_linkpk, slp.positionfk, slp.candidatefk, slp.created_by
      , slp.status, slp.date_completed, slp.date_created as ccm_create_date, slp.active, slp.candidatefk as candidate, slc._sys_status as candidate_status';
      $query .= ' FROM sl_meeting slm';
      $query .= ' INNER JOIN sl_meeting m2 ON m2.candidatefk = slm.candidatefk AND m2.meeting_done = 1';
      $query .= ' INNER JOIN sl_position_link slp on slp.candidatefk = slm.candidatefk ';
      $query .= ' INNER JOIN sl_candidate slc on slc.sl_candidatepk = slp.candidatefk';
      $query .= ' WHERE slm.created_by IN ('.implode(',', $user_ids).')
      AND slp.status >= 51 AND slm.meeting_done = 1
      GROUP BY slp.sl_position_linkpk';
                  //AND date_created >= "'.$start_date.'"
                  //AND date_created <= "'.$end_date.'"';
    }

/*if ($group == 'researcher'){
  echo '<br><br><br>';
  var_dump($query);
}*/
    //else
    /*{
      $query = 'SELECT sl_meeting.date_met, sl_position_link.positionfk, sl_position_link.candidatefk, sl_position_link.status,';
      $query .= ' sl_position_link.date_created as ccm_create_date, sl_meeting.created_by';
      $query .= ' FROM sl_meeting';
      $query .= ' INNER JOIN sl_position_link ON sl_meeting.candidatefk = sl_position_link.candidatefk';
      $query .= ' AND sl_position_link.status >= 51';
      $query .= ' WHERE sl_meeting.created_by IN ('.implode(',', $user_ids).')';
      $query .= ' AND sl_meeting.meeting_done = 1
                  AND sl_position_link.date_created >= "'.$start_date.'"
                  AND sl_position_link.date_created <= "'.$end_date.'"';
    }*/

    $query .= ' ORDER BY ccm_create_date DESC';


   /* echo "<br><br><br><br>";
    echo $query;
    echo "<br><br><br><br>";*/

    $db_result = $this->oDB->executeQuery($query);
    $read = $db_result->readFirst();

    while($read)
    {
      $row = $db_result->getData();

      $positionfk = $row['positionfk'];
      $candidatefk = $row['candidatefk'];

      $create_date = strtotime($row['ccm_create_date']);
      $date_completed = strtotime($row['date_completed']);

      $query = "SELECT * FROM sl_position_link slp WHERE slp.candidatefk = ".$candidatefk." AND slp.status = 151 AND slp.positionfk = ".$positionfk;
      $result = $this->oDB->executeQuery($query);
      $read_inner = $result->readFirst();

      $control_flag = true;
      while($read_inner)
      {
        $row_inner = $result->getData();
        $control_date = strtotime($row_inner['date_created']);
//echo '<br><br>';
        //echo 'control date: '.$control_date;
        //echo '<br><br>';
        //echo 'complete date: '.$date_completed;

        if($control_date == $date_completed)
        {
          $control_flag = false;
        }
        //echo '<br><br>';
        //echo $control_flag;
        $read_inner = $result->readNext();
      }

      $diff = $date_completed - $create_date;
      $diff = floor($diff/(60*60*24)); // gun cinsinden veriyor...

      /*if($diff > 180)
      {
        echo $row['sl_position_linkpk'].' : ';
        echo $create_date.' - ';
        echo $date_completed.' = ';
        echo $diff;
        echo "<br><br>";
      }*/

      if ($row['status'] > 51)
      {
        $status = $row['status'];

        if(isset($repeating_info[$row['created_by']][$status][$row['candidatefk']]))
        {
          $read = $db_result->readNext();
          continue;
        }
        else
          $repeating_info[$row['created_by']][$status][$row['candidatefk']] = '';
      }

      if (!isset($ccm_data[$row['created_by']]['ccm1']))
      {
        $ccm_data[$row['created_by']]['ccm1'] = 0;
        $ccm_data[$row['created_by']]['ccm1_done'] = 0;
        $ccm_data[$row['created_by']]['ccm2'] = 0;
        $ccm_data[$row['created_by']]['ccm2_done'] = 0;
        $ccm_data[$row['created_by']]['mccm'] = 0;
        $ccm_data[$row['created_by']]['mccm_done'] = 0;
        $ccm_data[$row['created_by']]['ccm_info']['ccm1'] = array();
        $ccm_data[$row['created_by']]['ccm_info']['ccm2'] = array();
        $ccm_data[$row['created_by']]['ccm_info']['mccm'] = array();
      }
      if (!isset($ccm_data[$row['meeting_created_by']]['ccm1']))
      {
        $ccm_data[$row['meeting_created_by']]['ccm1'] = 0;
        $ccm_data[$row['meeting_created_by']]['ccm1_done'] = 0;
        $ccm_data[$row['meeting_created_by']]['ccm2'] = 0;
        $ccm_data[$row['meeting_created_by']]['ccm2_done'] = 0;
        $ccm_data[$row['meeting_created_by']]['mccm'] = 0;
        $ccm_data[$row['meeting_created_by']]['mccm_done'] = 0;
        $ccm_data[$row['meeting_created_by']]['ccm_info']['ccm1'] = array();
        $ccm_data[$row['meeting_created_by']]['ccm_info']['ccm2'] = array();
        $ccm_data[$row['meeting_created_by']]['ccm_info']['mccm'] = array();
      }

      $array_key = '';

      $row_create_date = strtotime($row['ccm_create_date']);
      $row_complete_date = strtotime($row['date_completed']);
      $control_start_date = strtotime($start_date);
      $control_end_date = strtotime($end_date);
      $researcher_date_flag = true;

      if($group == 'researcher')
      {
        if($temp['min_date'] != $temp['sl_meetingpk'])
        {
            $researcher_date_flag = false;
        }
      }

      if ($row['status'] == 51 && $row['candidate_status'] == 0 && $researcher_date_flag)
      {


        $array_key = $row['positionfk'].$row['candidatefk'].'_51_'.$row['sl_position_linkpk'];

        //if (strtotime($row['ccm_create_date']) >= $start_date_stamp &&
        //  strtotime($row['ccm_create_date']) <= $end_date_stamp)
        if($row_create_date >= $control_start_date && $row_create_date <= $control_end_date)
        {
          $ccm_data[$row['created_by']]['ccm1'] += 1;
          $ccm_data[$row['created_by']]['ccm_info']['ccm1'][$array_key] = array('candidate' => $row['candidatefk'],
            'date' => $row['ccm_create_date'], 'ccm_position' => $row['positionfk']);

          if($group == 'researcher' && $row['created_by'] != $row['meeting_created_by'])
          {
            $ccm_data[$row['meeting_created_by']]['ccm1'] += 1;
            $ccm_data[$row['meeting_created_by']]['ccm_info']['ccm1'][$array_key] = array('candidate' => $row['candidatefk'],
              'date' => $row['ccm_create_date'], 'ccm_position' => $row['positionfk']);
          }
        }
        if($row['active'] == 0 && $row_complete_date >= $control_start_date && $row_complete_date <= $control_end_date && $diff < 184 && $control_flag)
        {
          if($group == 'consultant')
          {
            $ccm_data[$row['created_by']]['ccm1_done'] += 1;
            $ccm_data[$row['created_by']]['ccm_info']['ccm1'][$array_key]['ccm_done_candidate'] = $row['candidatefk'];
          }

          if($group == 'researcher' && $row['created_by'] != $row['meeting_created_by'])
          {
            $ccm_data[$row['meeting_created_by']]['ccm1_done'] += 1;
            $ccm_data[$row['meeting_created_by']]['ccm_info']['ccm1'][$array_key]['ccm_done_candidate'] = $row['candidatefk'];
          }
          /*if($row['candidatefk'] == '206311')
          {
            echo '<br><br><br><br><br><br><br>';
            echo $group.'<br>';
            echo $row['created_by'].'<br>';
            echo 'GIRDI'.'<br>';
            echo $row['candidatefk'].'<br>';
            echo $ccm_data[$row['created_by']]['ccm1_done'].'<br>';
          }*/
        }
      }
      else if ($row['status'] == 52 && $row['candidate_status'] == 0 && $researcher_date_flag)
      {
        $array_key = $row['positionfk'].$row['candidatefk'].'_52_'.$row['sl_position_linkpk'];

        //if (strtotime($row['ccm_create_date']) >= $start_date_stamp &&
          //strtotime($row['ccm_create_date']) <= $end_date_stamp)
        //{
          //$previous_ccm_key = $row['positionfk'].$row['candidatefk'].'_51';

          /*if (!empty($ccm_data[$row['created_by']]['ccm_info']['ccm1'][$previous_ccm_key]) &&
            isset($ccm_keys[$previous_ccm_key]) && strtotime($ccm_keys[$previous_ccm_key]) >= $start_date_stamp &&
            strtotime($ccm_keys[$previous_ccm_key]) <= $end_date_stamp)

          {
            $ccm_data[$row['created_by']]['ccm1_done'] += 1;
            $ccm_data[$row['created_by']]['ccm_info']['ccm1'][$previous_ccm_key]['ccm_done_candidate'] = $row['candidatefk'];
          }*/
          if($row_create_date>= $control_start_date && $row_create_date <= $control_end_date)
          {
            $ccm_data[$row['created_by']]['ccm2'] += 1;
            $ccm_data[$row['created_by']]['ccm_info']['ccm2'][$array_key] = array('candidate' => $row['candidatefk'],
              'date' => $row['ccm_create_date'], 'ccm_position' => $row['positionfk']);

            if($group == 'researcher' && $row['created_by'] != $row['meeting_created_by'])
            {
              $ccm_data[$row['meeting_created_by']]['ccm2'] += 1;
              $ccm_data[$row['meeting_created_by']]['ccm_info']['ccm2'][$array_key] = array('candidate' => $row['candidatefk'],
                'date' => $row['ccm_create_date'], 'ccm_position' => $row['positionfk']);
            }
          }

          if($row['active'] == 0 && $row_complete_date >= $control_start_date && $row_complete_date <= $control_end_date && $diff < 180 && $control_flag)
          {
            $ccm_data[$row['created_by']]['ccm2_done'] += 1;
            $ccm_data[$row['created_by']]['ccm_info']['ccm2'][$array_key]['ccm_done_candidate'] = $row['candidatefk'];

            if($group == 'researcher' && $row['created_by'] != $row['meeting_created_by'])
            {
              $ccm_data[$row['meeting_created_by']]['ccm2_done'] += 1;
              $ccm_data[$row['meeting_created_by']]['ccm_info']['ccm2'][$array_key]['ccm_done_candidate'] = $row['candidatefk'];
            }
          }

        //}
      }
      else if ($row['status'] > 52 && $row['status'] <= 61 && $row['candidate_status'] == 0 && $researcher_date_flag)
      {
        $array_key = $row['positionfk'].$row['candidatefk'].$row['status'].'_mccm_'.$row['status'];

        //if (strtotime($row['ccm_create_date']) >= $start_date_stamp &&
         // strtotime($row['ccm_create_date']) <= $end_date_stamp)
        //{
          //$previous_ccm_key = $row['positionfk'].$row['candidatefk'].'_51';

          /*if (empty($ccm_data[$row['created_by']]['ccm_info']['ccm1'][$previous_ccm_key]['ccm_done_candidate']) &&
            isset($ccm_keys[$previous_ccm_key]) && strtotime($ccm_keys[$previous_ccm_key]) >= $start_date_stamp &&
            strtotime($ccm_keys[$previous_ccm_key]) <= $end_date_stamp)
          {
            $ccm_data[$row['created_by']]['ccm1_done'] += 1;
            $ccm_data[$row['created_by']]['ccm_info']['ccm1'][$previous_ccm_key]['ccm_done_candidate'] = $row['candidatefk'];
          }*/

          //$previous_ccm_key = $row['positionfk'].$row['candidatefk'].'_52';

          /*if (empty($ccm_data[$row['created_by']]['ccm_info']['ccm2'][$previous_ccm_key]['ccm_done_candidate']) &&
            isset($ccm_keys[$previous_ccm_key]) && strtotime($ccm_keys[$previous_ccm_key]) >= $start_date_stamp &&
            strtotime($ccm_keys[$previous_ccm_key]) <= $end_date_stamp)
          {
            $ccm_data[$row['created_by']]['ccm2_done'] += 1;
            $ccm_data[$row['created_by']]['ccm_info']['ccm2'][$previous_ccm_key]['ccm_done_candidate'] = $row['candidatefk'];
          }*/

          $previous_ccm_key = $row['positionfk'].$row['candidatefk'].'_mccm_'.$row['status'];

          if($row_create_date>= $control_start_date && $row_create_date <= $control_end_date)
          {
            $ccm_data[$row['created_by']]['mccm'] += 1;
            $ccm_data[$row['created_by']]['ccm_info']['mccm'][$previous_ccm_key] = array('candidate' => $row['candidatefk'],
              'date' => $row['ccm_create_date'], 'ccm_position' => $row['positionfk']);

            if($group == 'researcher' && $row['created_by'] != $row['meeting_created_by'])
            {
              $ccm_data[$row['meeting_created_by']]['mccm'] += 1;
              $ccm_data[$row['meeting_created_by']]['ccm_info']['mccm'][$previous_ccm_key] = array('candidate' => $row['candidatefk'],
                'date' => $row['ccm_create_date'], 'ccm_position' => $row['positionfk']);
            }
          }

          if($row['active'] == 0 && $row_complete_date >= $control_start_date && $row_complete_date <= $control_end_date && $diff < 180 && $control_flag)
          {
            //$ccm_data[$row['created_by']]['mccm_done'] += 1;
            //$ccm_data[$row['created_by']]['ccm_info']['mccm'][$array_key]['ccm_done_candidate'][$row['status']] = $row['candidatefk'];

            $ccm_data[$row['created_by']]['mccm_done'] += 1;
            $ccm_data[$row['created_by']]['ccm_info']['mccm'][$previous_ccm_key]['ccm_done_candidate'][$row['status']] = $row['candidatefk'];

            if($group == 'researcher' && $row['created_by'] != $row['meeting_created_by'])
            {
              $ccm_data[$row['meeting_created_by']]['mccm_done'] += 1;
              $ccm_data[$row['meeting_created_by']]['ccm_info']['mccm'][$previous_ccm_key]['ccm_done_candidate'][$row['status']] = $row['candidatefk'];
            }
          }

          /*if (!empty($ccm_data[$row['created_by']]['ccm_info']['mccm'][$previous_ccm_key]) &&
            empty($ccm_data[$row['created_by']]['ccm_info']['mccm'][$previous_ccm_key]['ccm_done_candidate'][$row['status']]) &&
            isset($ccm_keys[$previous_ccm_key]) && $row['status'] > 53 && strtotime($ccm_keys[$previous_ccm_key]) >= $start_date_stamp &&
            strtotime($ccm_keys[$previous_ccm_key]) <= $end_date_stamp)
          {
            $ccm_data[$row['created_by']]['mccm_done'] += 1;
            $ccm_data[$row['created_by']]['ccm_info']['mccm'][$previous_ccm_key]['ccm_done_candidate'][$row['status']] = $row['candidatefk'];
          }*/
        //}
      }
      else if($row['status'] == 101 && $row['candidate_status'] == 0) // revenue chart ve kpi da researcher lar icin yazdik
      {
        if($row_create_date >= $control_start_date && $row_create_date <= $control_end_date)
        {
            $previous_ccm_key = $row['positionfk'].$row['candidatefk'].'_placed_revenue';

            $ccm_data[$row['created_by']]['placedRevenue'] += 1;
            //$ccm_data[$row['created_by']]['placedRevenue_info']['placedRevenue'][$previous_ccm_key]['candidate'][$row['status']] = $row['candidatefk'];

            $ccm_data[$row['created_by']]['placedRevenue_info']['placedRevenue'][$previous_ccm_key] = array('candidate' => $row['candidatefk'],
              'date' => $row['ccm_create_date'], 'ccm_position' => $row['positionfk']);

            if($group == 'researcher' && $row['created_by'] != $row['meeting_created_by'])
            {
              $ccm_data[$row['meeting_created_by']]['placedRevenue'] += 1;
              //$ccm_data[$row['meeting_created_by']]['placedRevenue_info']['placedRevenue'][$previous_ccm_key]['candidate'][$row['status']] = $row['candidatefk'];

              $ccm_data[$row['meeting_created_by']]['placedRevenue_info']['placedRevenue'][$previous_ccm_key] = array('candidate' => $row['candidatefk'],
              'date' => $row['ccm_create_date'], 'ccm_position' => $row['positionfk']);
            }
        }
      }
      //else
      //{
       // if (strtotime($row['ccm_create_date']) >= $start_date_stamp &&
        //  strtotime($row['ccm_create_date']) <= $end_date_stamp)
        //{
          //$previous_ccm_key = $row['positionfk'].$row['candidatefk'].'_51';

          /*if (empty($ccm_data[$row['created_by']]['ccm_info']['ccm1'][$previous_ccm_key]['ccm_done_candidate']) &&
            isset($ccm_keys[$previous_ccm_key]) && strtotime($ccm_keys[$previous_ccm_key]) >= $start_date_stamp &&
            strtotime($ccm_keys[$previous_ccm_key]) <= $end_date_stamp)
          {
            $ccm_data[$row['created_by']]['ccm1_done'] += 1;
            $ccm_data[$row['created_by']]['ccm_info']['ccm1'][$previous_ccm_key]['ccm_done_candidate'] = $row['candidatefk'];
          }*/

          //$previous_ccm_key = $row['positionfk'].$row['candidatefk'].'_52';

          /*if (empty($ccm_data[$row['created_by']]['ccm_info']['ccm2'][$previous_ccm_key]['ccm_done_candidate']) &&
            isset($ccm_keys[$previous_ccm_key]) && strtotime($ccm_keys[$previous_ccm_key]) >= $start_date_stamp &&
            strtotime($ccm_keys[$previous_ccm_key]) <= $end_date_stamp)
          {
            $ccm_data[$row['created_by']]['ccm2_done'] += 1;
            $ccm_data[$row['created_by']]['ccm_info']['ccm2'][$previous_ccm_key]['ccm_done_candidate'] = $row['candidatefk'];
          }*/

          //$previous_ccm_key = $row['positionfk'].$row['candidatefk'].'_mccm';

          //if (empty($ccm_data[$row['created_by']]['ccm_info']['mccm'][$previous_ccm_key]['ccm_done_candidate'][100]) &&
          //  isset($ccm_keys[$previous_ccm_key]) && strtotime($ccm_keys[$previous_ccm_key]) >= $start_date_stamp &&
           // strtotime($ccm_keys[$previous_ccm_key]) <= $end_date_stamp)
          //if($row['active'] == 0)
          //{
          //  $ccm_data[$row['created_by']]['mccm_done'] += 1;
         //   $ccm_data[$row['created_by']]['ccm_info']['mccm'][$previous_ccm_key]['ccm_done_candidate'][100] = $row['candidatefk'];
          //}
        //}
      //}

      if (!empty($array_key))
        $ccm_keys[$array_key] = $row['ccm_create_date'];

      $read = $db_result->readNext();
    }

//var_dump($ccm_data);
    return $ccm_data;
  }

  public function get_resume_sent($user_ids, $start_date, $end_date, $group = 'researcher')
  {
    $resume_sent_info = array();

    if ($group == 'consultant')
    {
      $query = 'SELECT spl.positionfk, spl.candidatefk, spl.created_by, spl.date_created as resume_sent_date
      , min(spl2.sl_position_linkpk) as control, spl.sl_position_linkpk, spl.status';
      $query .= ' FROM sl_position_link spl';
      $query .= ' INNER JOIN sl_position_link spl2 ON spl.candidatefk = spl2.candidatefk AND (spl2.status = 2)';
      $query .= ' WHERE spl.created_by IN ('.implode(',', $user_ids).')';
      $query .= ' AND spl.date_created BETWEEN "'.$start_date.'" AND "'.$end_date.'"';
      $query .= ' AND (spl.status = 2 OR spl.status = 51) GROUP BY spl.candidatefk, spl.positionfk';
    }
    else
    {
      $query = 'SELECT sl_meeting.date_met, spl.positionfk, spl.candidatefk, min(spl2.sl_position_linkpk) as control
                , spl.sl_position_linkpk, spl.status,';
      $query .= ' spl.date_created as resume_sent_date, sl_meeting.created_by ';
      $query .= ' FROM sl_meeting';
      $query .= ' INNER JOIN sl_position_link spl ON sl_meeting.candidatefk = spl.candidatefk AND (spl.status = 2 OR spl.status = 51)';
      $query .= ' INNER JOIN sl_position_link spl2 ON sl_meeting.candidatefk = spl2.candidatefk AND (spl2.status = 2)';
      $query .= ' AND spl.date_created BETWEEN "'.$start_date.'" AND "'.$end_date.'"';
      $query .= ' WHERE sl_meeting.created_by IN ('.implode(',', $user_ids).')';
      $query .= ' AND sl_meeting.meeting_done = 1 GROUP BY spl.candidatefk, spl.positionfk';
    }

/*if ($group == 'consultant'){
  echo '<br><br><br>';
  var_dump($query);
}*/

    $db_result = $this->oDB->executeQuery($query);
    $read = $db_result->readFirst();

    while ($read)
    {
      $row = $db_result->getData();

      $index = $row['candidatefk'].'_'.$row['positionfk'];

      if (!isset($resume_sent_info[$row['created_by']]['resumes_sent']))
      {
        $resume_sent_info[$row['created_by']]['resumes_sent'] = 0;
        $resume_sent_info[$row['created_by']]['resumes_sent_info'] = array();
      }

      if ($group == 'researcher')
      {
        if (isset($resume_sent_info[$row['created_by']][$index]))
        {
          $read = $db_result->readNext();
          continue;
        }
        else
        {
          $resume_sent_info[$row['created_by']][$index] = '';
        }
      }

      if($row['status'] >= 51 && $row['control'] == null)
      {
        $resume_sent_info[$row['created_by']]['resumes_sent'] += 1;
        $resume_sent_info[$row['created_by']]['resumes_sent_info'][] = array('candidate' => $row['candidatefk'],
          'date' => $row['resume_sent_date']);
      }
      else if($row['status'] == 2)
      {
        $resume_sent_info[$row['created_by']]['resumes_sent'] += 1;
        $resume_sent_info[$row['created_by']]['resumes_sent_info'][] = array('candidate' => $row['candidatefk'],
          'date' => $row['resume_sent_date']);
      }

      $read = $db_result->readNext();
    }

    return $resume_sent_info;
  }

  public function get_new_in_play($user_ids, $start_date, $end_date, $group = 'researcher')
  {
    $new_in_play_info = array();

    // gets new_candidates_in_play START

    $add = " ";
    if($group == 'researcher')
    {
      $add = " AND m.meeting_done = 1 ";
    }

    $query = 'SELECT min(pl2.sl_position_linkpk) as min_date_position, pl.sl_position_linkpk, pl.created_by as pl_created_by ,m.*, min(m2.sl_meetingpk) as min_date, pl.status as pl_status, pl.active as pl_active, slc._sys_status as candidate_status
        ,pl.date_completed , pl.date_created as ccm_create_date
        FROM sl_meeting m
        INNER JOIN sl_meeting m2 ON m2.candidatefk = m.candidatefk
        INNER JOIN sl_position_link pl ON pl.candidatefk = m.candidatefk
        INNER JOIN sl_candidate slc on slc.sl_candidatepk = m.candidatefk AND slc._sys_status = 0
        INNER JOIN sl_position_link pl2 ON pl2.candidatefk = pl.candidatefk
        WHERE pl.date_completed >= "'.$start_date.'"
        AND pl.date_completed <= "'.$end_date.'"
        AND pl.status = 51
        AND pl.active = 0
        AND pl2.status = 51
        AND pl2.active = 0
        AND slc._sys_status = 0
        '.$add.'
        group by pl.candidatefk, pl.positionfk
        order by m.candidatefk';

//echo '<br><br>';

    $oDbResult = array();

    $oDbResult = $this->oDB->executeQuery($query);
    $read = $oDbResult->readFirst();

    while($read)
    {
      $temp = $oDbResult->getData();

      $create_date = strtotime($temp['ccm_create_date']);
      $date_completed = strtotime($temp['date_completed']);

      $diff = $date_completed - $create_date;
      $diff = floor($diff/(60*60*24)); // gun cinsinden veriyor...

      if($temp['min_date'] == $temp['sl_meetingpk'] && $temp['min_date_position'] == $temp['sl_position_linkpk'] && $temp['meeting_done'] == 1 && $temp['pl_status'] >= 51 && $temp['pl_active'] == 0 && $diff < 180)
      {
        if($group == 'researcher')
        {
          $user = $temp['created_by'];
        }
        else
        {
          $user = $temp['pl_created_by'];
        }
        if(isset($new_in_play_info[$user]['new_candidates']))
        {
          array_push($new_in_play_info[$user]['new_candidates'], $temp);
        }
        else
        {
          $new_in_play_info[$user]['new_candidates'] = array();
          array_push($new_in_play_info[$user]['new_candidates'], $temp);
        }
        //$asData[$temp['created_by']] = $temp;
      }
      $read = $oDbResult->readNext();
    }
    //var_dump($new_in_play_info['314']['new_candidates']);
    // gets new_candidates_in_play END

    // gets new_positions_in_play START
    $query = 'SELECT m.*, min(m2.sl_meetingpk) as min_date, pl.status as pl_status, pl.active as pl_active, pl.created_by as pl_created_by, pl.sl_position_linkpk,
        min(pl2.sl_position_linkpk) as min_date_position, pl.positionfk as positionfk, slc._sys_status as candidate_status
        ,pl.date_completed , pl.date_created as ccm_create_date
        FROM sl_meeting m
        INNER JOIN sl_candidate slc on slc.sl_candidatepk = m.candidatefk AND slc._sys_status = 0
        INNER JOIN sl_meeting m2 ON m2.candidatefk = m.candidatefk
        INNER JOIN sl_position_link pl ON pl.candidatefk = m.candidatefk
        INNER JOIN sl_position_link pl2 ON pl2.positionfk = pl.positionfk
        WHERE pl.date_completed >= "'.$start_date.'"
        AND pl.date_completed <= "'.$end_date.'"
        AND pl.status = 51
        AND pl.active = 0
        AND pl2.status = 51
        AND pl2.active = 0
        '.$add.'
        AND slc._sys_status = 0
        group by pl.candidatefk, pl.positionfk
        order by m.candidatefk';

//echo '<br><br>';
//var_dump($query);

    $oDbResult = array();

    $oDbResult = $this->oDB->executeQuery($query);
    $read = $oDbResult->readFirst();

    while($read)
    {
      $temp = $oDbResult->getData();

      $create_date = strtotime($temp['ccm_create_date']);
      $date_completed = strtotime($temp['date_completed']);

      $diff = $date_completed - $create_date;
      $diff = floor($diff/(60*60*24)); // gun cinsinden veriyor...

      if($temp['min_date'] == $temp['sl_meetingpk'] && $temp['min_date_position'] == $temp['sl_position_linkpk'] && $temp['pl_status'] == 51 && $temp['pl_active'] == 0 && $diff < 180) // && $diff < 180 geri koydum neden ciktiysa
      {
        if($group == 'researcher')
        {
          $user = $temp['created_by'];
        }
        else
        {
          $user = $temp['pl_created_by'];
        }

        if(isset($new_in_play_info[$user]['new_positions']))
        {
          array_push($new_in_play_info[$user]['new_positions'], $temp);
        }
        else
        {
          $new_in_play_info[$user]['new_positions'] = array();
          array_push($new_in_play_info[$user]['new_positions'], $temp);
        }
        //$asData[$temp['created_by']] = $temp;
      }
      $read = $oDbResult->readNext();
    }
    // gets new_positions_in_play END


    //$new_in_play_info = array();

    /*if ($group == 'consultant')
    {
      $query = 'SELECT pl.positionfk, pl.candidatefk, pl.created_by, pl.status, pl.date_created, min(pl2.sl_position_linkpk), pl.sl_position_linkpk';
      $query .= ' FROM sl_position_link pl INNER JOIN sl_position_link pl2 on pl2.candidatefk = pl.candidatefk';
      $query .= ' WHERE pl.status = 51 AND pl.active != 1 GROUP BY pl.sl_position_linkpk ORDER BY pl.candidatefk';
    }
    else
    {
      $query = 'SELECT sl_meeting.date_met, sl_position_link.positionfk, sl_position_link.candidatefk, sl_position_link.status,';
      $query .= ' sl_position_link.date_created, sl_meeting.created_by';
      $query .= ' FROM sl_meeting';
      $query .= ' INNER JOIN sl_position_link ON sl_meeting.candidatefk = sl_position_link.candidatefk';
      $query .= ' AND sl_position_link.status = 51 AND sl_position_link.active != 1';
      // $query .= ' AND sl_position_link.date_created BETWEEN "'.$start_date.'" AND "'.$end_date.'"';
      // $query .= ' WHERE sl_meeting.created_by IN ('.implode(",", $user_ids).')';
      $query .= ' WHERE sl_meeting.meeting_done = 1';
    }*/

    /*$db_result = $this->oDB->executeQuery($query);
    $read = $db_result->readFirst();
var_dump($db_result->getData());
exit;
    $temp_new_candidate = $temp_new_position = array();

    while ($read)
    {
      $row = $db_result->getData();

      if (empty($temp_new_candidate[$row['candidatefk']])
        || strtotime($temp_new_candidate[$row['candidatefk']]['date_created']) > strtotime($row['date_created']) )
      {
        $temp_new_candidate[$row['candidatefk']] = array('date_created' => $row['date_created'],
          'created_by' => $row['created_by']);
      }

      if (empty($temp_new_position[$row['positionfk']])
        || strtotime($temp_new_position[$row['positionfk']]['date_created']) > strtotime($row['date_created']) )
      {
        $temp_new_position[$row['positionfk']] = array('date_created' => $row['date_created'],
          'created_by' => $row['created_by']);
      }

      $read = $db_result->readNext();
    }

    foreach ($temp_new_candidate as $key => $value)
    {
      if (empty($new_in_play_info[$value['created_by']]['new_candidates']))
      {
        $new_in_play_info[$value['created_by']]['new_candidates'] = 0;
        $new_in_play_info[$value['created_by']]['in_play_info']['new_candidates'] = array();
      }

      if (strtotime($value['date_created']) >= strtotime($start_date)
        && strtotime($value['date_created']) <= strtotime($end_date))
      {
        $new_in_play_info[$value['created_by']]['new_candidates'] += 1;
        $new_in_play_info[$value['created_by']]['in_play_info']['new_candidates'][] = array('candidate' => $key,
        'date' => $value['date_created']);
      }
    }

    foreach ($temp_new_position as $key => $value)
    {
      if (empty($new_in_play_info[$value['created_by']]['new_positions']))
      {
        $new_in_play_info[$value['created_by']]['new_positions'] = 0;
        $new_in_play_info[$value['created_by']]['in_play_info']['new_positions'] = array();
      }

      if (strtotime($value['date_created']) >= strtotime($start_date)
        && strtotime($value['date_created']) <= strtotime($end_date))
      {
        $new_in_play_info[$value['created_by']]['new_positions'] += 1;
        $new_in_play_info[$value['created_by']]['in_play_info']['new_positions'][] = array('candidate' => $key,
        'date' => $value['date_created']);
      }
    }
*/
    return $new_in_play_info;
  }

  public function get_offer_sent($user_ids, $start_date, $end_date, $group = 'researcher')
  {
    $offers_info = array();

    if ($group == 'consultant')
    {
      $query = 'SELECT slp.positionfk, slp.candidatefk, slp.created_by, slc._sys_status as candidate_status';
      $query .= ' FROM sl_position_link slp';
      $query .= ' INNER JOIN sl_candidate slc on slc.sl_candidatepk = slp.candidatefk AND slc._sys_status = 0';
      $query .= ' WHERE slp.created_by IN ('.implode(',', $user_ids).')';
      $query .= ' AND slp.date_created BETWEEN "'.$start_date.'" AND "'.$end_date.'"';
      $query .= ' AND (slp.status = 100 OR slp.status = 101) GROUP BY slp.candidatefk, slp.positionfk';
    }
    else
    {
      $query = 'SELECT sl_position_link.positionfk, sl_position_link.candidatefk, sl_meeting.created_by, slc._sys_status as candidate_status ';
      $query .= ' FROM sl_meeting';
      $query .= ' INNER JOIN sl_position_link ON sl_meeting.candidatefk = sl_position_link.candidatefk AND (sl_position_link.status = 100 OR sl_position_link.status = 101) AND sl_position_link.active != 0';
      $query .= ' INNER JOIN sl_candidate slc on slc.sl_candidatepk = sl_position_link.candidatefk AND slc._sys_status = 0';
      $query .= ' AND sl_position_link.date_created BETWEEN "'.$start_date.'" AND "'.$end_date.'"';
      $query .= ' WHERE sl_meeting.created_by IN ('.implode(',', $user_ids).')';
      $query .= ' AND sl_meeting.meeting_done = 1 GROUP BY sl_position_link.candidatefk, sl_position_link.positionfk';
    }

    $db_result = $this->oDB->executeQuery($query);
    $read = $db_result->readFirst();

    while ($read)
    {
      $row = $db_result->getData();

      if (!isset($offers_info[$row['created_by']]['offers_sent']))
      {
        $offers_info[$row['created_by']]['offers_sent'] = 0;
        $offers_info[$row['created_by']]['offer_info'] = array();
      }

      $offers_info[$row['created_by']]['offers_sent'] += 1;
      $offers_info[$row['created_by']]['offer_info'][] = array('candidate' => $row['candidatefk']);

      $read = $db_result->readNext();
    }

    return $offers_info;
  }

  public function get_placement_number($user_ids, $start_date, $end_date, $group = 'researcher')
  {
    $placed_info = array();

    if ($group == 'consultant')
    {
      $query = 'SELECT slp.positionfk, slp.candidatefk, slp.created_by, slc._sys_status as candidate_status';
      $query .= ' FROM sl_position_link slp';
      $query .= ' INNER JOIN sl_candidate slc on slc.sl_candidatepk = slp.candidatefk AND slc._sys_status = 0';
      $query .= ' WHERE slp.created_by IN ('.implode(',', $user_ids).')';
      $query .= ' AND slp.date_created BETWEEN "'.$start_date.'" AND "'.$end_date.'"';
      $query .= ' AND slp.status = 101';
    }
    else
    {
      $query = 'SELECT sl_position_link.positionfk, sl_position_link.candidatefk, sl_meeting.created_by, slc._sys_status as candidate_status';
      $query .= ' FROM sl_meeting';
      $query .= ' INNER JOIN sl_position_link ON sl_meeting.candidatefk = sl_position_link.candidatefk AND sl_position_link.status = 101';
      $query .= ' INNER JOIN sl_candidate slc on slc.sl_candidatepk = sl_position_link.candidatefk AND slc._sys_status = 0';
      $query .= ' AND sl_position_link.date_created BETWEEN "'.$start_date.'" AND "'.$end_date.'"';
      $query .= ' WHERE sl_meeting.created_by IN ('.implode(',', $user_ids).')';
      $query .= ' AND sl_meeting.meeting_done = 1';
    }

    $db_result = $this->oDB->executeQuery($query);
    $read = $db_result->readFirst();

    while ($read)
    {
      $row = $db_result->getData();

      if (!isset($placed_info[$row['created_by']]['placed']))
      {
        $placed_info[$row['created_by']]['placed'] = 0;
        $placed_info[$row['created_by']]['placed_info'] = array();
      }

      $placed_info[$row['created_by']]['placed'] += 1;
      $placed_info[$row['created_by']]['placed_info'][] = array('candidate' => $row['candidatefk']);

      $read = $db_result->readNext();
    }

    return $placed_info;
  }

  public function get_call_log_data($ignore_users, $start_date = '', $end_date = '')
  {
    $call_log_data = array();

    $ignore_users = implode(',', $ignore_users);

    $query = 'SELECT call_log.duration, call_log.calling_party, call_log.dialed_on_trunk, login.firstname, ';
    $query .= 'login.lastname, login.phone_ext, login.nationalityfk, sl_nationality.shortname AS nationality ';
    $query .= 'FROM call_log ';
    $query .= 'LEFT JOIN login ON login.phone_ext = call_log.calling_party ';
    $query .= 'LEFT JOIN sl_nationality ON login.nationalityfk = sl_nationality.sl_nationalitypk ';
    $query .= 'WHERE LENGTH(call_log.dialed_on_trunk) > 5 AND login.status = 1 AND login.loginpk NOT IN ('.$ignore_users.') ';

    if (!empty($start_date))
      $query .= 'AND call_log.date BETWEEN "'.$start_date.'" AND "'.$end_date.'" ';

    $query .= 'ORDER BY call_log.calling_party';

    $db_result = $this->oDB->executeQuery($query);
    $read = $db_result->readFirst();
    if ($read)
    {
      while($read)
      {
        $row = $db_result->getData();

        if (empty($call_log_data[$row['calling_party']]))
        {
          $name = substr($row['firstname'], 0, 1).'. '.$row['lastname'];
          $call_log_data[$row['calling_party']] = array('name' => $name, 'nationality' => $row['nationality'],
            'calling_party' => $row['calling_party'], 'calls' => 0, 'attempts' => 0);
        }

        if ($row['duration'] > 30)
        {
          $call_log_data[$row['calling_party']]['calls'] += 1;
        }

        $call_log_data[$row['calling_party']]['attempts'] += 1;

        $read = $db_result->readNext();
      }
    }

    uasort($call_log_data, sort_multi_array_by_value('calls', 'reverse') );

    return $call_log_data;
  }
}