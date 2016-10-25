<?php

/*
 * List of charts
 * https://slistem.devserv.com/index.php5?uid=555-006&ppa=ppam
 *
 *
 * TOKYO - MET
 * Q1: https://squirrel.slate.co.jp/index.php5?uid=555-006&ppa=ppal&ppt=stat&ppk=0&pg=0&stat_type=kpi&watercooler=1&chart_only=1&location=tokyo&chart=met&period=q1
 * S1: https://squirrel.slate.co.jp/index.php5?uid=555-006&ppa=ppal&ppt=stat&ppk=0&pg=0&stat_type=kpi&watercooler=1&chart_only=1&location=tokyo&chart=met&period=s1
 * YEAR: https://squirrel.slate.co.jp/index.php5?uid=555-006&ppa=ppal&ppt=stat&ppk=0&pg=0&stat_type=kpi&watercooler=1&chart_only=1&location=tokyo&chart=met&period=year
 * Month: https://squirrel.slate.co.jp/index.php5?uid=555-006&ppa=ppal&ppt=stat&ppk=0&pg=0&stat_type=kpi&watercooler=1&chart_only=1&location=tokyo&chart=met&period=month


   TOKYO - SET vs MET
 * S1: /index.php5?uid=555-006&ppa=ppal&ppt=stat&ppk=0&pg=0&stat_type=kpi&watercooler=1&chart_only=1&location=tokyo&chart=set_vs_met&period=s1
 *
 *
 * TOKYO - in_play
 * S1: /index.php5?uid=555-006&ppa=ppal&ppt=stat&ppk=0&pg=0&stat_type=kpi&watercooler=1&chart_only=1&location=tokyo&chart=in_play&period=s1
 *
 *
 * PLACEMENT
 * /index.php5?uid=555-006&ppa=ppal&ppt=stat&ppk=0&pg=0&stat_type=kpi&watercooler=1&chart_only=1&period=custom&start=2013-01-01&end=2014-10-10&location=all&chart=placement_grp
 */

require_once('component/sl_stat/sl_stat.class.php5');
class CSl_statEx extends CSl_stat
{
  private $_oPage;
  private $_oDisplay;
  private $casUserData;
  private $casAllUserData;
  private $casUserByGroup;
  private $casDefaultTarget;
  private $cbInAjax = false;
  private $cbWatercooler = false;

  private $casTokyoIp = array('118.243.81.245', '221.113.50.237', '118.243.81.248');
  private $casColor = array('pitched' => '#9ED1ED', 'resume sent' => '#9BE8A9',
          'CCM1' => '#5B7FFF', 'CCM2' => '#2525CE', 'CCM3' => '#6325CE', 'CCM4' => '#9025CE', 'CCM5' => '#E03C9C',
          'CCM6' => '#5B7FFF', 'CCM7' => '#2525CE', 'CCM8' => '#6325CE', 'CCM9' => '#9025CE', 'CCM10' => '#E03C9C',
          'CCM11' => '#5B7FFF', 'CCM12' => '#2525CE', 'CCM13' => '#6325CE', 'CCM14' => '#9025CE', 'CCM15' => '#E03C9C',
          'offer' => '#E2AC48', 'placed' => '#F9EB22', 'stalled' => '#FF6D6B', 'expired' => '#FFA144',
          'failed' => '#4F4F4F','not interested' => '#e6e6e6', 'fallen off' => '#d6d6d6', 'cancelled' => '#fff');

  private $casSerieColor = array('#2073CC','#F4D211','#7AA515','#B51B1B','#1aadce','#492970','#f28f43','#77a1e5','#c42525','#a6c96a',
      '#F2E124', '#DB74C4',

      '#2073CC','#F4D211','#7AA515','#B51B1B','#1aadce','#492970','#f28f43','#77a1e5','#c42525','#a6c96a',
      '#F2E124', '#DB74C4',
      '#2073CC','#F4D211','#7AA515','#B51B1B','#1aadce','#492970','#f28f43','#77a1e5','#c42525','#a6c96a',
      '#F2E124', '#DB74C4',
      '#2073CC','#F4D211','#7AA515','#B51B1B','#1aadce','#492970','#f28f43','#77a1e5','#c42525','#a6c96a',
      '#F2E124', '#DB74C4',
      '#2073CC','#F4D211','#7AA515','#B51B1B','#1aadce','#492970','#f28f43','#77a1e5','#c42525','#a6c96a',
      '#F2E124', '#DB74C4',
      '#2073CC','#F4D211','#7AA515','#B51B1B','#1aadce','#492970','#f28f43','#77a1e5','#c42525','#a6c96a',
      '#F2E124', '#DB74C4',
      '#2073CC','#F4D211','#7AA515','#B51B1B','#1aadce','#492970','#f28f43','#77a1e5','#c42525','#a6c96a',
      '#F2E124', '#DB74C4',
      '#2073CC','#F4D211','#7AA515','#B51B1B','#1aadce','#492970','#f28f43','#77a1e5','#c42525','#a6c96a',
      '#F2E124', '#DB74C4');

  private $casStatus = array();
  private $casTmpTarget = array();
  private $cnHeight = 0;
  private $cnWidth = 0;
  private $cbDisplayLegend = true;

  public function __construct()
  {
    $this->_oPage = CDependency::getCpPage();
    $this->_oDisplay = CDependency::getCpHtml();

    $oLogin = CDependency::getCpLogin();
    $this->casUserData = $oLogin->getUserData();

    $this->casAllUserData = $oLogin->getUserList(0, false, true);
    $this->casUserByGroup = $oLogin->getGroupMembers();

    $oPosition = CDependency::getComponentByName('sl_position');
    $this->casStatus = $oPosition->getStatusList();

    $this->casDefaultTarget = array('target_new' => 0, 'target_met' => 27, 'target_play' => 7, 'target_placed' => 3, 'target_position' => 5);

    return true;
  }

  public function getDefaultType()
  {
    return '';
  }

  public function getDefaultAction()
  {
    return CONST_ACTION_VIEW;
  }
  //====================================================================
  //  accessors
  //====================================================================

  private function _setCustomSize($pnHeight = 200, $pnWidth = 200)
  {
    $this->cnHeight = $pnHeight;
    $this->cnWidth = $pnWidth;
    return true;
  }

  //====================================================================
  //  interface
  //====================================================================



  //remove if the interface is not used
  public function getAjax()
  {
    $this->_processUrl();
    switch($this->csType)
    {
      case 'analyst':

        switch($this->csAction)
        {
          case CONST_ACTION_REFRESH:
           return json_encode($this->_oPage->getAjaxExtraContent(array('data' => $this->_getAnalystStat())));
           break;

          default:
            return json_encode($this->_oPage->getAjaxExtraContent(array('data' => $this->_getAnalystPage())));
        }
        break;


      default:
        switch($this->csAction)
        {
          case CONST_ACTION_LIST:
          case CONST_ACTION_VIEW:

              return json_encode($this->_oPage->getAjaxExtraContent(array('data' => convertToUtf8($this->_getStatPage(true)),
                'action' => '$(\'#statPageSectionRight\').scrollTop(0);')));
            break;
        }
        break;
    }

  }

  //remove if the interface is not used
  public function getHtml()
  {
    $this->_processUrl();

    switch($this->csAction)
    {
      case CONST_ACTION_LIST:
      case CONST_ACTION_VIEW:
        return $this->_getStatPage();
        break;

      case CONST_ACTION_MANAGE:
        return $this->_getGraphPageList();
        break;

      case ACTION_REVENUE_CHART:
        return $this->get_revenue_chart();
        break;

      case ACTION_SELECTED_REVENUE_CHART:
        return $this->getSelectedRevenueChart();
        break;

      case ACTION_CCM_CHART:
        return $this->get_ccm_chart();
        break;

      case ACTION_TOTALS_CHART:
        return $this->get_general_total_chart();
        break;

      case ACTION_CALL_LOG_CHART:
        return $this->get_call_log_chart();
        break;
    }

    return '';
  }


  public function getCronJob()
  {
    $this->_processUrl();

    $this->_getUserHomeChart();

    return '';
  }

  //====================================================================
  //  Component core
  //====================================================================




    //------------------------------------------------------
    //  Private methods
    //------------------------------------------------------


    private function _getStatPage($pbInJax = false)
    {

      $sDateStart = $original_date_start = getValue('date_start');
      if(strlen($sDateStart) == 7)
        $sDateStart.='-01';

      if(empty($sDateStart) || !is_date($sDateStart))
      {
        //$sDateStart = date('Y-m', strtotime('-3 months')).'-01';
        $sDateStart = date('Y-m', strtotime('-2 month')).'-01';
      }

      $sDateEnd = getValue('date_end');
      if(strlen($sDateEnd) == 7)
      {
        if ($original_date_start == $sDateEnd)
        {
          $sDateEnd = date("Y-m-t", strtotime($sDateEnd));
        }
        else
        {
          $sDateEnd.='-01';
        }
      }

      if(empty($sDateEnd) || !is_date($sDateEnd))
      {
        $sDateEnd = date('Y-m', strtotime('+1 month')).'-01';
      }

      $sDateStart .= ' 00:00:00';
      $sDateEnd .= ' 23:59:59';

      //$nUser = (int)getValue('loginfk', 0);
      if(!isset($_POST['loginfk']) || !is_listOfInt($_POST['loginfk']))
        $_POST['loginfk'] = array($this->casUserData['loginpk']);
      else
        $_POST['loginfk'] = explode(',', $_POST['loginfk']);

      //dump($_POST['loginfk']);
      $this->_oPage->addJsFile(self::getResourcePath().'/js/highchart_extend.js');
      $this->_oPage->addCssFile($this->getResourcePath().'/css/sl_stat.css');

      $oLogin = CDependency::getCpLogin();
      //dump($asUser);


      $sHTML = '';
      $this->cbInAjax = $pbInJax;
      $this->cnWindowSize = 1;
      $nGroup = (int)getValue('groupfk', 0);
      $group_name = getValue('group_name', 'researcher');
      $bChartOnly = (bool)getValue('chart_only', 0);
      $sChartType = getValue('chart_type', 'column');
      $this->cbWatercooler = (bool)getValue('watercooler');

      $asUrlOption = array('groupfk' => $nGroup, 'chart_only' => (int)$bChartOnly, 'chart_type' => $sChartType, 'watercooler' => (int)$this->cbWatercooler);


      if(!empty($this->cbWatercooler))
      {
        //add class to hide everything except charts
        $this->_oPage->addCssFile($this->getResourcePath().'/css/watercooler.css');
        $this->cnWindowSize++;
      }

      //to display KPIs
      if($bChartOnly && !empty($this->cbWatercooler))
      {
        $this->cnWindowSize++;
        $sLocation = getValue('location');
        $sTeam = getValue('team');

        // GROUP 116:
        // real users. Sub section of "active user" that help differenciate "real person" from mailing or admin accounts

        if(!empty($sTeam))
          $asUser = $oLogin->getUserByTeam(0, $sTeam);
        else
        {
          switch($sLocation)
          {
            case 'all':
              $asUser = $oLogin->getUserInMultiGroups(array(116), true);
              break;

            case 'tokyo':
              //$asUser = $asUser = $oLogin->getUserByTeam(0, 'office_tokyo');
              $asUser = $oLogin->getUserInMultiGroups(array(106,116), true);
              break;

            case 'manila':
              //$asUser = $oLogin->getUserByTeam(0, 'office_manila');
              $asUser = $asUser = $oLogin->getUserInMultiGroups(array(107,116), true);
              break;

            case 'canada':
              //$asUser = $oLogin->getUserByTeam(0, 'office_manila');
              $asUser = $asUser = $oLogin->getUserInMultiGroups(array(113,116), true);
              break;

            case 'hongkong':
              $asUser = $asUser = $oLogin->getUserInMultiGroups(array(110,116), true);
              break;

            case 'singapore':
              $asUser = $asUser = $oLogin->getUserInMultiGroups(array(117,116), true);
              break;

            case 'grp':
              $sTeam = getValue('grp');
              $asTeam = explode(',',$sTeam);
              $asTeam = cast_arrayOfInt($asTeam);
              if(!empty($asTeam))
                $asUser = $oLogin->getUserInMultiGroups($asTeam);

              break;
          }
        }

        $nUser = count($asUser);

        if($nUser >= 1)
          uasort($asUser, sort_multi_array_by_value('id'));

        $this->cbDisplayLegend = false;
        $sMainMax = '100%';
        $sSideMax = 0;
        $sTitle = '';
      }
      else
      {
        $asUser = $oLogin->getUserList($_POST['loginfk'], false, true);
        $nUser = count($asUser);

        if($nUser == 1)
        {
          $sUserLink = $oLogin->getUserLink(array_first_key($asUser));
          $sTitle = $sUserLink.'\'s';
        }
        else
        {
          $sTitle = $nUser.' users';
          uasort($asUser, sort_multi_array_by_value('id'));
        }
      }

      $sStatType = getValue('stat_type');

      //simplify accesses from menu
      if(empty($sStatType) && $this->csType == CONST_STAT_TYPE_PIPELINE)
        $sStatType = CONST_STAT_TYPE_PIPELINE;

      if(empty($sStatType) && $this->csType == CONST_STAT_TYPE_PIPEEXT)
        $sStatType = CONST_STAT_TYPE_PIPEEXT;

      if(empty($sStatType))
        $sStatType = 'sic';

      $sStatHTML = '';
      switch($sStatType)
      {
        case 'sic':
          $sTitle.= ' SIC chart ';
          $sStatHTML = $this->_getSicStat($sDateStart, $sDateEnd, $asUser, $nGroup, $sChartType);
          break;

        case 'global':
          $sTitle.= ' global statistics ';
          $sStatHTML = $this->_getGlobalStat($sDateStart, $sDateEnd, $asUser, $nGroup, $sChartType);
          break;

        case CONST_STAT_TYPE_PIPELINE:
          $sTitle.= ' pipeline ';
          $sStatHTML = $this->_getPipeline($sDateStart, $sDateEnd, $asUser, $nGroup, $sChartType);
          break;

        case CONST_STAT_TYPE_PIPEEXT:
          $sTitle.= ' full pipeline ';
          $sStatHTML = $this->_getPipelineDetails($sDateStart, $sDateEnd, $asUser, $nGroup, $sChartType);
          break;

        case CONST_STAT_TYPE_POSITION_PIPE:
          $sTitle.= ' position pipeline ';
          $sStatHTML = $this->_getPositionPipeline($sDateStart, $sDateEnd, $asUser, $nGroup, $sChartType);
          break;

        case CONST_STAT_TYPE_KPI:
          $sTitle.= ' KPI ';
          $sStatHTML = $this->_getKpi($sDateStart, $sDateEnd, $asUser, $nGroup, $sChartType);
          break;
      }

      //reloading the page in ajax, return only the forms
      if($bChartOnly)
      {
        return $this->_oDisplay->getBloc('', $sHTML .$sStatHTML, array('class' => 'kpi_container'));
      }


      if($pbInJax)
      {
        $sMainMax = '875';
        $sSideMax = '250';
        $asUrlOption['chart_only'] = 1;
        $bChartOnly = true;
      }
      else
      {
        $sMainMax = $sSideMax = '';
      }

      $oHTML = CDependency::getCpHtml();
      $sHTML.= $oHTML->getBlocStart('', array('class' => 'statChartContainer'));

      $sHTML.= $oHTML->getTitle($sTitle. ' ['.$sDateStart.' - '.$sDateEnd.']', 'h3', true);
      $sHTML.= $oHTML->getCR();



      //---------------------------------------------------------------------------
      //---------------------------------------------------------------------------
      //start creating the page
      //split the page in 2:


      //left section containing filtering form
      $sHTML.= $oHTML->getBlocStart('', array('class' => 'statPageSectionLeft', 'style' => 'width: 20%; min-width: 230px; max-width: '.$sSideMax.'px; '));

        if($pbInJax)
          $sURL = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_VIEW, '', 0, $asUrlOption);
        else
          $sURL = $this->_oPage->getUrl($this->csUid, CONST_ACTION_VIEW, '', 0, $asUrlOption);

        $oForm = $oHTML->initForm('statForm');
        $oForm->setFormParams('statForm', $pbInJax, array('action' => $sURL, 'ajaxTarget' => 'statPageSectionRight',  'submitLabel'=>'Get stat!', 'noCancelButton' => true));

        $oForm->addField('select', 'stat_type', array('label' => 'Chart Type', 'onchange' => '

          var vValue = $(this).val();

          if(vValue == \'pipex\' || vValue == \'popipe\' || vValue == \'sic\' || vValue == \'kpi\')
          {
            $(\'#chart_typeId\').attr(\'disabled\', \'disabled\').addClass(\'disabled\');
          }
          else
          {
            if(vValue == \'pipe\')
            {
              $(\'#chart_typeId option.pipe\').removeAttr(\'disabled\').show(0);
              $(\'#chart_typeId option:not(.pipe)\').attr(\'disabled\', \'disabled\').hide(0);
            }
            else
            {
              $(\'#chart_typeId option.pipe\').attr(\'disabled\', \'disabled\').hide(0);
              $(\'#chart_typeId option:not(.pipe)\').removeAttr(\'disabled\').show(0);
            }

            $(\'#chart_typeId\').removeAttr(\'disabled\').removeClass(\'disabled\');
            $(\'#chart_typeId option\').removeAttr(\'selected\');
            $(\'#chart_typeId option:visible(:first)\').attr(\'selected\', \'selected\');
            $(\'#chart_typeId\').change();
          }

          if(vValue != \'kpi\')
          {
            $(this).closest(\'form\').find(\'.date_selector\').removeClass(\'hidden\');
            $(this).closest(\'form\').find(\'.period_selector\').addClass(\'hidden\');
          }
          else
          {
            $(this).closest(\'form\').find(\'.period_selector\').removeClass(\'hidden\');
            $(this).closest(\'form\').find(\'.date_selector\').addClass(\'hidden\');
          }
          '));

        //onload, chnage() the select to activate/deactivate other fileds
        $sHTML.='<script>$(\'#stat_typeId\').change(); </script>';

        if($sStatType == 'global')
          $oForm->addOption('stat_type', array('label' => 'Global stat', 'value' => 'global', 'selected' => 'selected'));
        else
          $oForm->addOption('stat_type', array('label' => 'Global stat', 'value' => 'global'));

        if($sStatType == 'sic')
          $oForm->addOption('stat_type', array('label' => 'Sic charts', 'value' => 'sic', 'selected' => 'selected'));
        else
          $oForm->addOption('stat_type', array('label' => 'Sic charts', 'value' => 'sic'));

        if($sStatType == CONST_STAT_TYPE_PIPELINE)
        {
          $oForm->addOption('stat_type', array('label' => 'Pipeline (user)', 'value' => CONST_STAT_TYPE_PIPELINE, 'selected' => 'selected'));
        }
        else
          $oForm->addOption('stat_type', array('label' => 'Pipeline  (user)', 'value' => CONST_STAT_TYPE_PIPELINE));


        if($sStatType == CONST_STAT_TYPE_PIPEEXT)
        {
          $oForm->addOption('stat_type', array('label' => 'Pipeline (global)', 'value' => CONST_STAT_TYPE_PIPEEXT, 'selected' => 'selected'));
        }
        else
          $oForm->addOption('stat_type', array('label' => 'Pipeline (global)', 'value' => CONST_STAT_TYPE_PIPEEXT));

        if($sStatType == CONST_STAT_TYPE_POSITION_PIPE)
        {
          $oForm->addOption('stat_type', array('label' => 'Position Pipeline', 'value' => CONST_STAT_TYPE_POSITION_PIPE, 'selected' => 'selected'));
        }
        else
          $oForm->addOption('stat_type', array('label' => 'Position Pipeline', 'value' => CONST_STAT_TYPE_POSITION_PIPE));

        if($sStatType == CONST_STAT_TYPE_KPI)
        {
          $oForm->addOption('stat_type', array('label' => 'KPI', 'value' => CONST_STAT_TYPE_KPI, 'selected' => 'selected'));
        }
        else
          $oForm->addOption('stat_type', array('label' => 'KPI', 'value' => CONST_STAT_TYPE_KPI));




        $oForm->addField('select', 'chart_type', array('label' => 'Display data in'));

        if($sChartType == 'line')
          $oForm->addOption('chart_type', array('label' => 'lines', 'value' => 'line', 'selected' => 'selected'));
        else
          $oForm->addOption('chart_type', array('label' => 'lines', 'value' => 'line'));

        if($sChartType == 'column')
          $oForm->addOption('chart_type', array('label' => 'columns', 'value' => 'column', 'selected' => 'selected'));
        else
          $oForm->addOption('chart_type', array('label' => 'columns', 'value' => 'column'));

        if($sChartType == 'pie')
          $oForm->addOption('chart_type', array('label' => 'pies', 'value' => 'pie', 'class' => 'pipe', 'selected' => 'selected'));
        else
          $oForm->addOption('chart_type', array('label' => 'pies', 'value' => 'pie', 'class' => 'pipe'));

        if($sChartType == 'funnel')
          $oForm->addOption('chart_type', array('label' => 'funnels', 'value' => 'funnel', 'class' => 'pipe', 'selected' => 'selected'));
        else
          $oForm->addOption('chart_type', array('label' => 'funnels', 'value' => 'funnel', 'class' => 'pipe',));


        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        //Section relative to dates and kpi period selection
        $oForm->addField('misc', '', array('type' => 'text', 'text' => '<div class="separator"></div>'));


        $oForm->addField('select', 'period', array('label' => 'Display Kpi for', 'onchange' =>
          '
          var vValue = $(this).val();
          if(vValue == \'custom\')
          {
            $(this).closest(\'form\').find(\'.date_selector\').removeClass(\'hidden\');
          }
          else
          {
            $(this).closest(\'form\').find(\'.date_selector\').addClass(\'hidden\');
          }'));
        $oForm->setFieldDisplayParams('period', array('class' => 'period_selector hidden'));
        $oForm->addOption('period', array('label' => 'This month', 'value' => 'month'));
        $oForm->addOption('period', array('label' => 'Q1 '.date('Y'), 'value' => 'q1'));
        $oForm->addOption('period', array('label' => 'Q2 '.$this->_getYearWithStat(3), 'value' => 'q2'));
        $oForm->addOption('period', array('label' => 'Q3 '.$this->_getYearWithStat(6), 'value' => 'q3'));
        $oForm->addOption('period', array('label' => 'Q4 '.$this->_getYearWithStat(9), 'value' => 'q4'));
        $oForm->addOption('period', array('label' => 'S1 '.date('Y'), 'value' => 's1'));
        $oForm->addOption('period', array('label' => 'S2 '.$this->_getYearWithStat(6), 'value' => 's2'));
        $oForm->addOption('period', array('label' => 'All year', 'value' => 'year'));
        $oForm->addOption('period', array('label' => 'Custom dates', 'value' => 'custom'));


        $oForm->addField('input', 'date_start', array('type' => 'month', 'label' => 'From', 'value' => $sDateStart));
        $oForm->setFieldDisplayParams('date_start', array('class' => 'date_selector'));

        $oForm->addField('input', 'date_end', array('type' => 'month', 'label' => 'To', 'value' => $sDateEnd));
        $oForm->setFieldDisplayParams('date_end', array('class' => 'date_selector'));


        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        $oForm->addField('misc', '', array('type' => 'text', 'text' => '<div class="separator"></div>'));

        //Group management
        $oForm->addField('select', 'groupfk', array('label' => 'Group',
            'onchange' => '

              $(\'#loginfkId\').tokenInput(\'clear\');

              var asCons = $(this).val().split(\'||\');

              var group_name = $(this).children(\':selected\').text();
              $(\'#group_name\').val(group_name);

              $(asCons).each(function(nIndex, sValue)
              {
                var asValue = sValue.split(\'@@\');
                if(asValue.length == 2)
                {
                  //console.log(\'adding user \'+asValue[1]);
                  $(\'#loginfkId\').tokenInput(\'add\', {id: asValue[0], name: asValue[1]});
                }
              });

        '));
        $oForm->addOption('groupfk', array('label' => '-', 'value' => $this->casUserData['loginpk'].'@@'.$this->casUserData['id']));
        foreach($this->casUserByGroup as $asUData)
        {
          $asUserList = array();
          foreach($asUData as $nUserPk => $asUdetail)
            $asUserList[] = $asUdetail['loginpk'].'@@'.$asUdetail['id'];

          if($nGroup == $asUdetail['login_groupfk'])
            $oForm->addOption('groupfk', array('label' => $asUdetail['group_label'], 'value' => implode('||', $asUserList)));
          else
            $oForm->addOption('groupfk', array('label' => $asUdetail['group_label'], 'value' => implode('||', $asUserList)));
        }


        $sURL = $this->_oPage->getAjaxUrl('login', CONST_ACTION_SEARCH, CONST_LOGIN_TYPE_USER, 0, array('show_id' => 0, 'friendly' => 1));
        $oForm->addField('selector', 'loginfk', array('label' => 'Consultant', 'url' => $sURL, 'nbresult' => 30));

        foreach($asUser as $nUserPk => $asUserData)
        {
          $oForm->addOption('loginfk', array('label' => $asUserData['id'], 'value' => $nUserPk, 'selected' => 'selected'));
        }

        $oForm->addField('input', 'group_name', array('type' => 'hidden', 'value' => $group_name,
          'id' => 'group_name'));

        $sHTML.= $oForm->getDisplay();

      $sURL = $this->_oPage->getUrl('555-006', CONST_ACTION_VIEW, '', 0, array('chart_only' => 0, 'watercooler' => 1, 'target' => '_blank'));
      $sHTML.=  $oHTML->getCR();
      $sHTML.=  $oHTML->getLink('&nbsp;&nbsp;&nbsp;view in full page', $sURL, array('target' => '_blank'));

      $sHTML.= $oHTML->getBlocEnd();




      $sHTML.= $oHTML->getBlocStart('statPageSectionRight', array('class' => 'statPageSectionRight', 'style' => ' max-width: '.$sMainMax.'px;'));
      $sHTML.= $sStatHTML;
      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getFloatHack();
      $sHTML.= $oHTML->getBlocEnd();

      return $sHTML;
    }




    private function _getGlobalStat($sDateStart, $sDateEnd, $asUser, $nGroup, $psChartType = 'line')
    {
      $oChart = CDependency::getComponentByName('charts');
      $oChart->includeChartsJs();

      $sId = uniqid();

      //Inittialize variables
      $oDateStart = new DateTime($sDateStart);
      $oDateEnd = new DateTime($sDateEnd);
      $oInterval = $oDateEnd->diff($oDateStart);
      $nMonth = ((int)$oInterval->format('%y') * 12) + (int)$oInterval->format('%m') + 1;
      $anUser = array_keys($asUser);
      $asData = array();


      $asStatData = $this->_getModel()->getSicChartMet($anUser, $sDateStart, $sDateEnd);
      $nStartTime = strtotime($sDateStart);
      $nUser = count($asUser);

      foreach($asUser as $nUserPk => $asUData)
      {
        if(!isset($asStatData['target'][$nUserPk]))
          $asStatData['target'][$nUserPk] = $this->casDefaultTarget;

        for($nCount = 0; $nCount < $nMonth; $nCount++)
        {
          $sMonth = date('Y-m', strtotime('+'.$nCount.' month',$nStartTime));
          $asData['head'][$sMonth] = $sMonth;
          $asData['objective'][$sMonth] = 14;

          //----------------------------------------
          //create a monthly total for all users displayed
          if(!isset($asStatData[$nUserPk][$sMonth]))
            $asStatData[$nUserPk][$sMonth] = 0;

          if(isset($asStatData[$nUserPk][$sMonth][1]))
            $asData['row'][$asUData['pseudo']][$sMonth] = $asStatData[$nUserPk][$sMonth][1];
          else
            $asData['row'][$asUData['pseudo']][$sMonth] = 0;


          //----------------------------------------
          //create a monthly total for all users displayed
          if(!isset($asData['row']['total'][$sMonth]))
          {
            $asData['row']['total'][$sMonth] = 0;
            $asData['row']['total_target'][$sMonth] = 0;
          }

          if(isset($asStatData[$nUserPk][$sMonth][1]))
            $asData['row']['total'][$sMonth]+= $asStatData[$nUserPk][$sMonth][1];

          $asData['row']['total_target'][$sMonth]+= $asStatData['target'][$nUserPk]['target_met'];
        }
      }

      foreach($asData['row']['total'] as $sMonth => $nValue)
      {
        $asData['average'][$sMonth] = round(($nValue / $nUser), 2);
        $asData['objective'][$sMonth] = round(($asData['row']['total_target'][$sMonth] / $nUser));
      }

      //dump($asData);

      $sHTML = '
        <div id="globalChart_'.$sId.'"></div>
        <script>


        $(function () {
        $("#globalChart_'.$sId.'").highcharts({
            chart:
            {
              events:
              {
                load : edgeExtend,
                redraw : edgeExtend
              }
            },
            title: {
                text: "Candidates met / monthly",
                x: -20 //center
            },
            plotOptions:
            {
              line:
              {
                marker:
                {
                  enabled: false,
                  symbol: "circle",
                  radius: 1,
                  states: { hover: {  enabled: true } }
                }
              },
              spline:
              {
                marker:
                {
                  enabled: false,
                  symbol: "circle",
                  radius: 1,
                  states: { hover: {  enabled: true } }
                }
              }
            },
            xAxis: {
                '.$this->_getCategories($sDateStart, $sDateEnd).'
            },
            yAxis: {
              title: {
                  text: "Candidates met"
              }';

           if($psChartType == 'line')
           {
             $sHTML.= '
                , plotLines: [{
                    value: 0,
                    width: 1,
                    color: "#808080"
                }]
              },';

             $sType = 'spline';
           }
           else
           {
             $sHTML.= '
            },';

            $sType = 'column';
           }

            $sHTML.= '
            tooltip: {';

            if($nUser <= 3)
            {
              $sHTML.= '
              shared: true,
              /*crosshairs: true,*/';
            }
            else
            {
              $sHTML.= '
              shared: false,
              /*useHTML: true,
              formatter: function()
              {
                console.log(this);
                var sTip = "";
                sTip+= "<span style=\'color:"+this.points[0].series.color+"\'>" + this.points[0].series.name+ "</span>: "+this.points[0].point.y+" <br />";
                sTip+= "<span style=\'color:"+this.points[1].series.color+"\'>" + this.points[1].series.name+ "</span>: "+this.points[1].point.y+" <br />";
                return sTip;
              },*/ ';
            }

            $sHTML.= '
            },
            legend: {
                layout: "vertical",
                align: "right",
                verticalAlign: "middle",
                borderWidth: 0
            },
            series: [{
                type: "spline",
                name: "Grp average",
                color: "#729E11",
                data: ['.implode(',', $asData['average']).'],
                visible: true
            }, {
                type: "line",
                name: "Objectives",
                color: "#ff0000",
                data: ['.implode(',', $asData['objective']).'],
                showInLegend: true,
                visible: true

            }';
            $nCount = 0;
            foreach($asUser as $nUserPk => $asUData)
            {
              $sHTML.= ',{
                type: "'.$sType.'",
                name: "'.$asUData['pseudo'].'",
                data: ['.implode(',', $asData['row'][$asUData['pseudo']]).'],
                color: "'.$this->casSerieColor[$nCount].'"
              }';
              $nCount++;
            }

            $sHTML.= ']
        });
      });
      </script>';



      //-20 for padding  /   (780 -40) for extra space for the first row
      //$nWidth = (floor(730/(count($asData['head'])+2)) -25);
      $nWidth = floor(100/(count($asData['head'])+2)) - 3;

      $sHTML.= '
      <style>
      div.stat_row { width: 100%; }
      div.stat_row div{ width: '.$nWidth.'%;}
      </style>
        <div class="title h3">Group stat</div>
      <div class="group_stat">';


      $sHTML.= '<div class="stat_row stat_row_header"><div>-</div><div>'.implode('</div><div>', $asData['head']).'</div><div>Tot / Avg.</div></div>';
      $sHTML.= '<div class="stat_row stat_row_total"><div>Grp total</div><div>'.implode('</div><div>', $asData['row']['total']).'</div><div> '.array_sum($asData['row']['total']).'</div></div>';
      $sHTML.= '<div class="stat_row stat_row_average" style="border-bottom: 3px solid #bbb;"><div>Grp average</div><div>'.implode('</div><div>', $asData['average']).'</div><div>&nbsp;</div></div>';
      $sHTML.= '<div class="stat_row stat_row_objective"><div>Target</div><div>'.implode('</div><div>', $asData['objective']).'</div><div>&nbsp;</div></div>';


      foreach($asData['row'] as $sUser => $asValue)
      {
        if(substr($sUser, 0, 5) != 'total')
        {
          $nTotal = 0;
          foreach($asValue as $sKey => $nValue)
          {
            $nTotal+= $nValue;
            if($nValue >= $asData['objective'][$sKey])
              $asValue[$sKey] = '<span class="above_avg">'.$nValue.' <label>(+'.($nValue - $asData['objective'][$sKey]).')</label></span>';
            else
              $asValue[$sKey] = '<span class="below_avg">'.$nValue.'<label>('.($nValue - $asData['objective'][$sKey]).')</label></span>';
          }


          $sHTML.= '<div class="stat_row"><div>'.$sUser.'</div><div>'.implode('</div><div>', $asValue).'</div>
                <div>'.round(($nTotal / count($asValue)), 2).'</div>
              </div>';
        }
      }

     $sHTML.= '<div class="floatHack"></div>';
     $sHTML.= '</div>';


      return $sHTML;
    }

    public function getSicData($panUser, $psDateStart, $psDateEnd)
    {
      $group_name = strtolower(getValue('group_name', 'researcher'));
      $asStatData = array();
      $asStatData['target'] = $this->_getModel()->getSicChartTarget($panUser);
      // $asStatData['new'] = $this->_getModel()->getSicChartNew($panUser, $psDateStart, $psDateEnd);
      $asStatData['met'] = $this->_getModel()->getSicChartMet($panUser, $psDateStart, $psDateEnd, $group_name);
      $asStatData['play'] = $this->_getModel()->getSicChartPlay($panUser, $psDateStart, $psDateEnd);
      $asStatData['position'] = $this->_getModel()->getSicChartPosition($panUser, $psDateStart, $psDateEnd);

      return $asStatData;
    }


    private function _getSicStat($sDateStart, $sDateEnd, $asUser, $nGroup, $psChartType = 'line',
      $pbReturnHtml = true, $group_name = 'researcher')
    {
      $oDateStart = new DateTime($sDateStart);
      $oDateEnd = new DateTime($sDateEnd);
      $oInterval = $oDateEnd->diff($oDateStart);
      $nMonth = ((int)$oInterval->format('%y') * 12) + (int)$oInterval->format('%m') + 1;  //aug to dec ...displays aug and december stats

      $temp = array();

      $oChart = CDependency::getComponentByName('charts');
      $oChart->includeChartsJs(true);

      $oHTML = CDependency::getCpHtml();
      $sCategories = $this->_getCategories($sDateStart, $sDateEnd);

      $anUser = array_keys($asUser['user_data']);
      $user_id = $asUser['user_id'];
      $user_ids = $asUser['user_ids'];
      $asStatData = array();

      $asStatData['target'] = $this->_getModel()->getSicChartTarget($anUser);


      if (empty($GLOBALS['redis']->get('play_'.$group_name)))
      {
        $temp = $this->_getModel()->get_new_in_play(0, $sDateStart, $sDateEnd, $group_name);
        $GLOBALS['redis']->set('play_'.$group_name, json_encode($temp));
      }
      else
        $temp = json_decode($GLOBALS['redis']->get('play_'.$group_name), true);

      if (!empty($temp[$user_id]['in_play_info']['new_candidates']))
      {
        foreach ($temp[$user_id]['in_play_info']['new_candidates'] as $value)
        {
          $value_month = date('Y-m', strtotime($value['date']));

          if (!isset($asStatData['play'][$user_id][$value_month]))
            $asStatData['play'][$user_id][$value_month] = 0;

          $asStatData['play'][$user_id][$value_month] += 1;
        }
      }
      else
        $asStatData['play'][$user_id] = array();

      if (!empty($temp[$user_id]['in_play_info']['new_positions']))
      {
        foreach ($temp[$user_id]['in_play_info']['new_positions'] as $value)
        {
          $value_month = date('Y-m', strtotime($value['date']));

          if (!isset($asStatData['position'][$user_id][$value_month]))
            $asStatData['position'][$user_id][$value_month] = 0;

          $asStatData['position'][$user_id][$value_month] += 1;
        }
      }
      else
        $asStatData['position'][$user_id] = array();


      if (empty($GLOBALS['redis']->get('met_'.$group_name)))
      {
        $temp = $this->_getModel()->getKpiSetVsMet($user_ids, $sDateStart, $sDateEnd, $group_name);

        if (!empty($temp))
          $GLOBALS['redis']->set('met_'.$group_name, json_encode($temp));
      }
      else
        $temp = json_decode($GLOBALS['redis']->get('met_'.$group_name), true);


      if (!empty($temp[$user_id]['met_meeting_info']))
      {
        foreach ($temp[$user_id]['met_meeting_info'] as $value)
        {
          $value_month = date('Y-m', strtotime($value['date']));

          if (!isset($asStatData['met'][$user_id][$value_month]))
            $asStatData['met'][$user_id][$value_month] = 0;

          $asStatData['met'][$user_id][$value_month] += 1;
        }
      }
      else
        $asStatData['met'][$user_id] = array();

      if (!empty($temp[$user_id]['set_meeting_info']))
      {
        foreach ($temp[$user_id]['set_meeting_info'] as $value)
        {
          $value_month = date('Y-m', strtotime($value['date']));

          if (!isset($asStatData['set'][$user_id][$value_month]))
            $asStatData['set'][$user_id][$value_month] = 0;

          $asStatData['set'][$user_id][$value_month] += 1;
        }
      }
      else
        $asStatData['set'][$user_id] = array();


      $asUserData = array();
      // --------------------------------------------------
      //Time to merge all the different stats for each user
      $nStartTime = strtotime($sDateStart);


      foreach($asUser['user_data'] as $user_id => $user_data)
      {
        (isset($asStatData['target'][$user_id]))? '': $asStatData['target'][$user_id] = $this->casDefaultTarget;
        $asData = array();

        for($nCount = 0; $nCount < $nMonth; $nCount++)
        {
          $sMonth = date('Y-m', strtotime('+'.$nCount.' month',$nStartTime));

          (isset($asStatData['met'][$user_id][$sMonth]))? '': $asStatData['met'][$user_id][$sMonth] = 0;
          (isset($asStatData['set'][$user_id][$sMonth]))? '': $asStatData['set'][$user_id][$sMonth] = 0;

          (isset($asStatData['play'][$user_id][$sMonth]))? '': $asStatData['play'][$user_id][$sMonth] = 0;
          (isset($asStatData['position'][$user_id][$sMonth]))? '': $asStatData['position'][$user_id][$sMonth] = 0;

          $asData['met'][$sMonth] = $asStatData['met'][$user_id][$sMonth];
          $asData['not_met'][$sMonth] = $asStatData['set'][$user_id][$sMonth] - $asStatData['met'][$user_id][$sMonth];

          if ($asData['not_met'][$sMonth] < 0)
            $asData['not_met'][$sMonth] = 0;

          $asData['play'][$sMonth] = $asStatData['play'][$user_id][$sMonth];
          $asData['position'][$sMonth] = $asStatData['position'][$user_id][$sMonth];

          //target
          $asData['target_met'][$sMonth] = $asStatData['target'][$user_id]['target_met'];
          $asData['target_play'][$sMonth] = $asStatData['target'][$user_id]['target_play'];
          $asData['target_position'][$sMonth] = $asStatData['target'][$user_id]['target_position'];
        }

        $asUserData[$user_data['pseudo']] = $asData;
      }

      $nUser = count($asUserData);
      /*dump($asUserData);
      exit('-- -- -- ');*/

      $sId = uniqid();

      $sHTML = $oHTML->getTitle('Objectives', 'h3', true);
      $sHTML.= $oHTML->getCR();

      if($this->cnHeight)
        $sChart = '<div style="height: '.$this->cnHeight.'px; width: '.$this->cnWidth.'px;  margin: 0 auto;">';
      else
        $sChart = '<div style="height: 250px; width: 780px;  margin: 0 auto;">';

      $nDay = date('j');
      $month_total_days = date('t');

      $target_met = $asStatData['target'][$user_id]['target_met'];
      $target_play = $asStatData['target'][$user_id]['target_play'];
      $target_position = $asStatData['target'][$user_id]['target_position'];

      $nMet = $asStatData['met'][$user_id][$sMonth];
      $nPlay = $asStatData['play'][$user_id][$sMonth];
      $nPos = $asStatData['position'][$user_id][$sMonth];

      $nMetToDate = ceil(($target_met/$month_total_days) * $nDay) - $nMet;
      if ($nMetToDate < 0)
        $nMetToDate = 0;

      $nPlayToDate = ceil(($target_play/$month_total_days) * $nDay) - $nPlay;
      if ($nPlayToDate < 0)
        $nPlayToDate = 0;

      $nPosToDate = ceil(($target_position/$month_total_days) * $nDay) - $nPos;
      if ($nPosToDate < 0)
        $nPosToDate = 0;

      $nMetRatio = $nPlayRatio = $nPosRatio = 0;

      if (!empty($nMet))
        $nMetRatio = round(($nMet/$target_met)*100);

      if (!empty($nPlay))
        $nPlayRatio = round(($nPlay/$target_play)*100);

      if (!empty($nPos))
        $nPosRatio = round(($nPos/$target_position)*100);

      $sMetClass = $this->_getClassFromValue($nMet, $nMetToDate);
      $sPlayClass = $this->_getClassFromValue($nPlay, $nPlayToDate);
      $sPosClass = $this->_getClassFromValue($nPos, $nPosToDate);

      $sChart .= '
        <div class="obj-container">
          <div class="obj-row obj-header">
            <div class="obj-desc"></div>
            <div class="obj-value">Met *</div>
            <div class="obj-value">In play</div>
            <div class="obj-value">Positions **</div>
          </div>

          <div class="obj-row">
            <div class="obj-desc">Month target</div>
            <div class="obj-value">'.$target_met.'</div>
            <div class="obj-value">'.$target_play.'</div>
            <div class="obj-value">'.$target_position.'</div>
          </div>

          <div class="obj-row">
            <div class="obj-desc">Target to date</div>
            <div class="obj-value">'.$nMetToDate.'</div>
            <div class="obj-value">'.$nPlayToDate.'</div>
            <div class="obj-value">'.$nPosToDate.'</div>
          </div>

          <div class="obj-row">
            <div class="obj-desc">Current</div>
            <div class="obj-value '.$sMetClass.'">'.$nMet.'</div>
            <div class="obj-value '.$sPlayClass.'">'.$nPlay.'</div>
            <div class="obj-value '.$sPosClass.'">'.$nPos.'</div>
          </div>

          <div class="obj-row">
            <div class="obj-desc">%</div>
            <div class="obj-value '.$sMetClass.'">'.$nMetRatio.'%</div>
            <div class="obj-value '.$sPlayClass.'">'.$nPlayRatio.'%</div>
            <div class="obj-value '.$sPosClass.'">'.$nPosRatio.'%</div>
          </div>
        </div>

        <div class="portal-legend">
          <span style="color: #888; font-style: italic; font-size: 11px;">* Meeting created with the new meeting feature. </span><br />
          <span style="color: #888; font-style: italic; font-size: 11px;">** Newly active positions, having their first CCM. </span>
        </div>
        </div>
      ';

      $asHTML['objectives'] = $sChart;
      $sHTML.= $sChart;

      $sHTML.= $oHTML->getCR();
      $sHTML.= $oHTML->getTitle('Candidates met', 'h3', true);
      $sHTML.= $oHTML->getCR();

      if($this->cnHeight)
        $sChart = '<div id="sicChart_'.$sId.'" style="height: '.$this->cnHeight.'px; width: '.$this->cnWidth.'px;  margin: 0 auto;"></div>';
      else
        $sChart = '<div id="sicChart_'.$sId.'" style="height: 250px; width: 780px;  margin: 0 auto;"></div>';


      $sChart.= '
        <script>
        $(function ()
        {
          $("#sicChart_'.$sId.'").highcharts(
          {
            chart:
            {
              events :
              {
                load : edgeExtend,
                redraw : edgeExtend
              }
            },
            title: {
                text: ""
            },
            legend: {
                layout: "vertical",
                align: "right",
                verticalAlign: "middle",
                borderWidth: 0
            },
            plotOptions:
            {
              column:
              {
                stacking: "normal"
              },
              area:
              {
                marker:
                {
                  enabled: false,
                  symbol: "circle",
                  radius: 1,
                  states: {
                      hover: {
                          enabled: true
                      }
                  }
                },
                fillColor:
                {
                  linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1},
                  stops:
                  [
                    [0, "#FFF6E5"],
                    [1, "#FFEECE"]
                  ]
                },
                fillOpacity: 0.5,
                lineWidth: 0,
                shadow: false,
                threshold: null
              }
            },
            xAxis: {
              '.$sCategories.'
            },
            yAxis: ';

            if($psChartType == 'line')
            {
              $sChart.= '
                {
                  title:
                  {
                    text: "Meetings"
                  },
                  plotLines: [{
                     value: 0,
                     width: 1,
                     color: "#808080"
                 }]
               },';
            }
            else
            {
             $sChart.= '
             {
                title:
                {
                  text: "Meetings"
                },
             },';
            }

            $sChart.= '
            tooltip: {
                shared: '.(($nUser==1)? 'true' : 'false').',
                valueSuffix: " meeting(s)"
            },
            series: [{
                type: "line",
                color: "#000",
                name: "Target",
                data: ['.implode(',', $asData['target_met']).'],
                dashStyle: "ShortDash"
            },';


     $nCount = 0;
     foreach($asUserData as $sUser => $asData)
     {

       if(!empty($asData['not_met']) && $nUser == 1)
       {
          $sChart.= '
          {
            type: "'.$psChartType.'",
            name: "Not met",
            stack: "'.$sUser.'",
            data: ['.implode(',', $asData['not_met']).'],
            color: "#FF2224" ';

          $sChart.= '}, ';
       }

       $sChart.= '
            {
                type: "'.$psChartType.'",
                name: "Met - '.$sUser.'",
                stack: "'.$sUser.'",
                data: ['.implode(',', $asData['met']).'],
                color: "'.$this->casSerieColor[$nCount].'" ';

       if($psChartType != 'line' && $nUser == 1)
       {
         $sChart.= ',
          dataLabels:
          {
            enabled: true,
            rotation: 0,
            color: "#FFFFFF",
            align: "center",
            verticalAlign: "top"
          }';
       }

       $sChart.= '},';
       $nCount++;
     }



     $sChart.= ']
        });
      });
      </script>';

     $asHTML['met'] = $sChart;
     $sHTML.= $sChart;

     $sHTML.= $oHTML->getBlocStart('', array('class' => 'chart_legend'));
     $sHTML.= $oHTML->getText('Key:', array('class' => 'title')) . $oHTML->getCR();
     $sHTML.= $oHTML->getText('Displays the number of meetings set "done" for the period and user(s) selected.', array('class' => 'light'));
     $sHTML.= $oHTML->getBlocEnd();


      $sHTML.= $oHTML->getCR();
      $sHTML.= $oHTML->getTitle('New candidates in play', 'h3', true);
      $sHTML.= $oHTML->getCR();

      if($this->cnHeight)
        $sChart = '<div id="sicChart2_'.$sId.'" style="height: '.$this->cnHeight.'px; width: '.$this->cnWidth.'px;  margin: 0 auto;"></div>';
      else
        $sChart = '<div id="sicChart2_'.$sId.'" style="height: 250px; width: 780px;  margin: 0 auto;"></div>';

      $sChart.= '
        <script>
        $(function () {
        $("#sicChart2_'.$sId.'").highcharts(
          {
            chart:
            {
              events :
              {
                load : edgeExtend,
                redraw : edgeExtend
              }
            },
            title: {
                text: ""
            },
            legend: {
                layout: "vertical",
                align: "right",
                verticalAlign: "middle",
                borderWidth: 0
            },
            plotOptions:
            {
              area:
              {
                marker:
                {
                  enabled: false,
                  symbol: "circle",
                  radius: 1,
                  states: {
                      hover: {
                          enabled: true
                      }
                  }
                },
                fillColor:
                {
                  linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1},
                  stops:
                  [
                    [0, "#FFF6E5"],
                    [1, "#FFEECE"]
                  ]
                },
                fillOpacity: 0.5,
                lineWidth: 0,
                shadow: false,
                threshold: null
              }
            },
            xAxis: {
                '.$sCategories.'
            },
            yAxis: ';

            if($psChartType == 'line')
            {
              $sChart.= '
                {
                  title:
                  {
                    text: "Candidate"
                  },
                  plotLines: [{
                     value: 0,
                     width: 1,
                     color: "#808080"
                 }]
               },';
            }
            else
            {
             $sChart.= '
             {
                title:
                {
                  text: "Candidate"
                },
             },';
            }

            $sChart.= '
            tooltip: {
              shared: false,
              valueSuffix: " candidate(s)"
            },
            series: [{
                type: "line",
                color: "#000",
                name: "Target",
                data: ['.implode(',', $asData['target_play']).'],
                dashStyle: "ShortDash"
            },';

     $nCount = 0;
     foreach($asUserData as $sUser => $asData)
     {
       $sChart.= '
            {
                type: "'.$psChartType.'",
                name: "'.$sUser.'",
                data: ['.implode(',', $asData['play']).'],
                color: "'.$this->casSerieColor[$nCount].'" ';

       if($psChartType != 'line' && $nUser == 1)
       {
         $sChart.= ',
                dataLabels:
                {
                  enabled: true,
                  color: "#FFFFFF",
                  align: "center",
                  verticalAlign: "top"
                }';
       }
       $sChart.= '},';
       $nCount++;
     }

     $sChart.= ']
        });
      });
      </script>';

     $asHTML['play'] = $sChart;
     $sHTML.= $sChart;


      $sHTML.= $oHTML->getCR();
      $sHTML.= $oHTML->getTitle('New positions in play', 'h3', true);
      $sHTML.= $oHTML->getCR();

      if($this->cnHeight)
        $sChart = '<div id="sicChart3_'.$sId.'" style="height: '.$this->cnHeight.'px; width:'.$this->cnWidth.'px;  margin: 0 auto;"></div>';
      else
        $sChart = '<div id="sicChart3_'.$sId.'" style="height: 250px; width: 780px;  margin: 0 auto;"></div>';

       $sChart.= '
        <script>
        $(function () {
        $("#sicChart3_'.$sId.'").highcharts(
          {
            chart:
            {
              events :
              {
                load : edgeExtend,
                redraw : edgeExtend
              }
            },
            title: {
                text: ""
            },
            legend:
            {
              layout: "vertical",
              align: "right",
              verticalAlign: "middle",
              borderWidth: 0
            },
            plotOptions:
            {
              area:
              {
                marker:
                {
                  enabled: false,
                  symbol: "circle",
                  radius: 1,
                  states: {
                      hover: {
                          enabled: true
                      }
                  }
                },
                fillColor:
                {
                  linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1},
                  stops:
                  [
                    [0, "#FFF6E5"],
                    [1, "#FFEECE"]
                  ]
                },
                fillOpacity: 0.5,
                lineWidth: 0,
                shadow: false,
                threshold: null
              }
            },
            xAxis: {
                '.$sCategories.'
            },
            yAxis: ';

            if($psChartType == 'line')
            {
              $sChart.= '
                {
                  title:
                  {
                    text: "Positions"
                  },
                  plotLines: [{
                     value: 0,
                     width: 1,
                     color: "#808080"
                 }]
               },';
            }
            else
            {
             $sChart.= '
             {
                title:
                {
                  text: "Positions"
                },
             },';
            }

            $sChart.= '
            tooltip: {
              shared: true,
              valueSuffix: " position(s)"
            },
            series: [{
                type: "line",
                color: "#000",
                name: "Target",
                data: ['.implode(',', $asData['target_position']).'],
                dashStyle: "ShortDash"
            },';

      $nCount = 0;
      foreach($asUserData as $sUser => $asData)
      {
        $sChart.= '
             {
                 type: "'.$psChartType.'",
                 name: "'.$sUser.'",
                 data: ['.implode(',', $asData['position']).'],
                 color: "'.$this->casSerieColor[$nCount].'" ';

        if($psChartType != 'line' && $nUser == 1)
        {
          $sChart.= ',
                 dataLabels:
                 {
                   enabled: true,
                   color: "#FFFFFF",
                   align: "center",
                   verticalAlign: "top"
                 }';
        }
        $sChart.= '},';
        $nCount++;
      }

      $sChart.= ']

        });
      });
      </script>';


     $asHTML['position'] = $sChart;
     $sHTML.= $sChart;

      if($pbReturnHtml)
        return $sHTML;

      return $asHTML;
    }


    private function _getCategories($psDateStart, $psDateEnd)
    {
      $oDateStart = new DateTime($psDateStart);
      $oDateEnd = new DateTime($psDateEnd);
      $oInterval = $oDateEnd->diff($oDateStart);
      $nMonth = ((int)$oInterval->format('%y') * 12) + (int)$oInterval->format('%m') + 1;

      $nTimeStart = strtotime($psDateStart);

      $asMonth = array();
      for($nCount = 0; $nCount < $nMonth; $nCount++)
      {
         $asMonth[] = '"'.date('M', strtotime('+'.$nCount.' month', $nTimeStart)).'"';
      }
      $sCategory = 'categories: ['.implode(',', $asMonth).']';

      return $sCategory;
    }


    private function _getPipeline($psDateStart, $psDateEnd, $asUser, $pnGroup, $psChartType, $pbOnlyGlobal = false)
    {
      $oChart = CDependency::getComponentByName('charts');
      $oChart->includeChartsJs(false, true);
      $oHTML = CDependency::getCpHtml();
      $oLogin = CDependency::getCplogin();
      $sHTML = '';

      if($psChartType == 'pie')
        $nMaxStatus = 0;
      else
        $nMaxStatus = 100;

      $anUser = array_keys($asUser);

      if($pbOnlyGlobal || $psDateEnd > date('Y-m').'-01')
        $asMainData = $this->_getModel()->getPiplelinePieData($anUser, $psDateStart, $psDateEnd, $nMaxStatus, true);
      else
        $asMainData = array();

      if($pbOnlyGlobal)
        $asData = array();
      else
        $asData = $this->_getModel()->getPiplelinePieData($anUser, $psDateStart, $psDateEnd, 101, false);

      /*dump($asMainData);
      dump($asData);*/

      $asMainPieData = array();
      $asPieData = array();

      if(!empty($asMainData))
      {
        foreach($asMainData as $sMonth => $asStatData)
        {
          foreach($asStatData as $nStatus => $nNumber)
          {
            if(!isset($asMainPieData[$nStatus]))
              $asMainPieData[$nStatus] = 0;

            $asMainPieData[$nStatus]+= $nNumber;
          }
        }
        arsort($asMainPieData);
      }


      foreach($asData as $sMonth => $asStatData)
      {
        foreach($asStatData as $nStatus => $nNumber)
        {
          $asPieData[$sMonth][$nStatus] = $nNumber;
        }
      }
      //dump($asMainPieData);
      //dump($asPieData);

      //$nTotal = array_sum($asData);
      $sData = '';
      foreach($asMainPieData as $sKey => $nValue)
      {
        $sStatus = $this->casStatus[$sKey];
        if(empty($sData))
        {
          //detach the first slice
          $sData.= '{name: "'.$sStatus.'", y: '.$nValue.', color: "'.$this->casColor[$sStatus].'", sliced: true }';
        }
        else
          $sData.= ',{name: "'.$sStatus.'", y: '.$nValue.', color: "'.$this->casColor[$sStatus].'", sliced: false }';

      }


      $sId = uniqid();
      if($psChartType == 'pie')
      {
        $sSerieType = 'type: "pie", ';
        $sChartSettings = '
          legend:
          {
            layout: "vertical",
            align: "right",
            verticalAlign: "top",
            x: 0,
            y: -5,
            borderWidth: 0,
            backgroundColor: "#fff",
            floating: true,
            shadow: true,
            padding: 3,
            margin: 15,
            itemDistance: 15
          },
          plotOptions:
          {
            pie:
            {
              allowPointSelect: true,
              dataLabels:
              {
                enabled: true,
                allowPointSelect: true,
                cursor: "pointer",
                //distance: -50,
                connector: false,
                connectorColor: "transparent",
                format: "{point.y} {point.name} "
              },
              showInLegend: true,
              /*startAngle: -90,
              endAngle: 90,
              center: ["50%", "75%"]*/
            }
          },
          chart:
          {
            plotBackgroundColor: null,
            plotBorderWidth: 0,
            plotShadow: false,
            marginTop: 15
          },
          tooltip: {
               pointFormat: "<b>{point.y}</b> candidates ({point.percentage:.1f}%)"
          },';
      }
      else
      {
        $sSerieType = '';
        $sChartSettings = '
          chart:
          {
              type: "funnel",
              marginRight: 125,
              marginTop: 20
          },
          plotOptions:
          {
            series:
            {
              dataLabels: {
                  enabled: true,
                  format: "{point.name} <b>({point.y:,.0f})</b>",
                  color: "#555555",
                  softConnector: false,
                  showInLegend: true,
                  connectorColor: "#e6e6e6",
                  size: "8px"
              },
              neckWidth: "30%",
              neckHeight: "30%"
            }
          },';
      }

      if(!empty($asMainData))
      {
        $sHTML.= '
            <div id="pipelineChart_'.$sId.'" style="height: 300px; width: 550px; margin: 0 auto;"></div><script>
            $(function () {

            console.log("create pipeline chart #pipelineChart_'.$sId.'");


            $("#pipelineChart_'.$sId.'").highcharts({
                '.$sChartSettings.'
                title:
                {
                  text: "Current status of the pipeline <br />",
                  align: "left",
                  verticalAlign: "top"
                },
                series: [{
                   '.$sSerieType.'
                    name: "Candidate ",
                    color: "#5485B9",
                    data: ['.$sData.']
                }]
            });
          });
          </script><br class="floatHack"/>';
      }

      if($pbOnlyGlobal)
        return $sHTML;


      if(count($asPieData) > 1)
      {
        $nWidth = floor((780 / count($asPieData)))-15;
        if($nWidth < 390)
          $nWidth = 390;

        foreach($asPieData as $sMonth => $asData)
        {
          //ksort($asData);
          //arsort($asData);
          $asData = $this->_nbStatusSort($asData);


          $sId = uniqid();
          $sHTML.= '
            <div id="pipelineChart_'.$sId.'" style="height: 200px; width: '.$nWidth.'px; margin: 5px 5px 0 0; float: left;"></div><script>
            $(function () {
            $("#pipelineChart_'.$sId.'").highcharts({
              '.$sChartSettings.'
              title:
              {
                text: "Pipeline snapshot for '.date('M Y', strtotime($sMonth.'-01')).'",
                verticalAlign: "top",
                align: "left",
                x: -5,
                y: 3
              },
              series: [{
                '.$sSerieType.'
                name: "Candidate repartititon",
                color: "#5485B9",
                data: [';

              foreach($asData as $nStatus => $nValue)
              {
                $sStatus = $this->casStatus[$nStatus];
                $sHTML.= '{name: "'.$sStatus.'", y: '.$nValue.', color: "'.$this->casColor[$sStatus].'"},';
              }

         $sHTML.= ']
                }]
            });
          });
          </script>';
        }
      }


      $asCandidate = $this->_getModel()->getPiplelineCandidate($anUser, $psDateStart, $psDateEnd);
      $asHead = array('Candidate', 'Position title', 'Status', 'Consultant', 'Expires on');

      $sHTML.= $oHTML->getFloatHack();
      $sHTML.= $oHTML->getCR();
      $sHTML.= $oHTML->getBlocStart('', array('style' => 'float: left; margin: 10px 15px 0 5px;'));

      $sHTML.= $oHTML->getTitle('Active candidates in the pipeline ! ('.count($asCandidate).')', 'h3', true);
      $sHTML.= $oHTML->getCR();

        $sHTML.= $oHTML->getBlocStart('', array('class' => 'pipelineList group_stat'));

        $sHTML.= '<div class="stat_row stat_row_header"><div>'.implode('</div><div>', $asHead).'</div></div>';
        foreach($asCandidate as $asData)
        {
          $sURL = $this->_oPage->getAjaxUrl('555-001', CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, (int)$asData['candidatefk']);
          $sPositionURL = $this->_oPage->getAjaxUrl('555-005', CONST_ACTION_VIEW, CONST_POSITION_TYPE_JD, (int)$asData['positionfk']);
          $asRowData = array(
            '<div class="alignleft"><a href="javascript:;" onclick="popup_candi(this, \''.$sURL.'\');" >#'.$asData['candidatefk'].' - '.$asData['firstname'].' '.$asData['lastname'].'</a></div>',
            '<div class="alignleft"><a href="javascript:;" onclick="view_position(\''.$sPositionURL.'\');" >'.'#'.$asData['positionfk'].' - '.$asData['position_title'].'</a></div>',
            '<div style="text-align: center; color: #CE5A21;">'.$this->casStatus[(int)$asData['position_status']].'</div>',
            '<div style="text-align: center;">'.$oLogin->getUserLink((int)$asData['created_by'], true).'</div>',
            '<div>'.date('Y-m-d', strtotime($asData['date_expire'])).'</div>');

          $sHTML.= '<div class="stat_row">'.implode('', $asRowData).'</div>';
        }

        $sHTML.= $oHTML->getBlocEnd();
        $sHTML.= '<div class="floatHack"></div>';

      $sHTML.= $oHTML->getBlocEnd();
      return $sHTML;
    }


    private function _nbStatusSort(&$pasData)
    {
      arsort($pasData);

      $asSortedArray = $asResult = array();

      foreach($pasData as $nStatus => $nValue)
      {
        if(!isset($asSortedArray[$nValue]))
          $asSortedArray[$nValue] = array($nStatus);
        else
        {
          $asSortedArray[$nValue][] = $nStatus;
        }
      }

      foreach($asSortedArray as $nValue => $anStatus)
      {
        sort($anStatus);
        foreach($anStatus as $nStatus)
          $asResult[$nStatus] = $nValue;
      }

      //dump($asResult);
      //dump(' - - - - -');

      return $asResult;
    }


    private function _getPipelineDetails($psDateStart, $psDateEnd, $asUser, $pnGroup)
    {
      $oChart = CDependency::getComponentByName('charts');
      $oChart->includeChartsJs(false, false);
      $oHTML = CDependency::getCpHtml();
      $anStatus = array_keys($this->casStatus);

      $anUser = array_keys($asUser);
      $asChartData = $this->_getModel()->getPiplelineDetailData($anUser, $psDateStart, $psDateEnd, 250);
      $asPositionList = $this->_getModel()->getPiplelineDetails($anUser, $psDateStart, $psDateEnd, 200);

      /*dump($asChartData);
      dump(' - - - - - ');
      dump($asPositionList);*/

      $sHTML = $oHTML->getBlocStart('', array('class' => 'pipe_detail_container'));

      if(count($asChartData) > 1)
      {
        $sHTML.= $oHTML->getTitle('Global stats', 'h3', true);
        $sHTML.= $this->_getDetailUserChart($psDateStart, $psDateEnd, $asChartData, 'All users', true);
        $sHTML.= $oHTML->getFloatHack();
        $sHTML.= $oHTML->getCR(2);

        $sHTML.= $oHTML->getTitle('User stats', 'h3', true);
      }


      foreach($asPositionList as $nUserPk => $asCandidateData)
      {
        $sUserName = $this->casAllUserData[$nUserPk]['pseudo'];

        $sHTML.= $oHTML->getBlocStart('', array('class' => 'pipe_detail_row'));
        $sHTML.= $oHTML->getTitle($sUserName, 'h3', true);


        //$sHTML.= $this->_getDetailUserChart($psDateStart, $psDateEnd, $asChartData[$nUserPk], $sUserName);

          $sHTML.= $oHTML->getBlocStart('', array('class' => 'pipe_details'));

          foreach($asCandidateData as $asPosition)
          {
            //dump($asPosition);
            $sHTML.= $oHTML->getBlocStart('', array('class' => 'pipe_position_row'));

              $sHTML.= $oHTML->getBlocStart('', array('class' => 'pipe_data'));

                $sHTML.= $oHTML->getBlocStart();
                $sURL = $this->_oPage->getAjaxUrl('555-001', CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, $asPosition['data']['candidatefk']);
                $sHTML.= $this->_oDisplay->getLink($asPosition['data']['firstname'].' '.$asPosition['data']['lastname'], $sURL).' (#'.$asPosition['data']['candidatefk'].')';
                $sHTML.= $oHTML->getBlocEnd();


                //$sURL = $this->_oPage->getAjaxUrl('555-005', CONST_ACTION_VIEW, CONST_POSITION_TYPE_JD, $asPosition['data']['positionfk']);
                $asPosition['data']['position_title'] = mb_strimwidth($asPosition['data']['position_title'], 0, 25, '...');
                $sHTML.=  $oHTML->getBloc('', '['.$asPosition['data']['active'].'] Position #'.$asPosition['data']['positionfk'].' - '.$asPosition['data']['position_title']);

                $sHTML.= $oHTML->getBlocStart();
                $sURL = $this->_oPage->getAjaxUrl('555-001', CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_COMP, $asPosition['data']['sl_companypk']);
                $sHTML.= 'for '.$this->_oDisplay->getLink($asPosition['data']['company_name'], $sURL);
                $sHTML.= $oHTML->getBlocEnd();

              $sHTML.= $oHTML->getBlocEnd();

              $nSteps = count($asPosition['status']) - 1;
              $nCount = 0;
              foreach($anStatus as $nStatus)
              {
                if(isset($asPosition['status'][$nStatus]))
                {
                  //text in a tooltip
                  $sDate = date('d-M y', strtotime($asPosition['status'][$nStatus]));
                  $sTitle = $sDate.': '.$this->casStatus[$nStatus];
                  $sText = '<br /><br />'.$sDate;

                  $sHTML.= $oHTML->getBloc('', $sText, array('title' => $sTitle, 'class' => 'pipe_status_icon pipe_status_icon'.$nStatus));

                  if($nCount < $nSteps)
                    $sHTML.= $oHTML->getBloc('', $sText, array('class' => 'pipe_date'));

                  $nCount++;
                }
              }

            $sHTML.= $oHTML->getFloatHack();
            $sHTML.= $oHTML->getBlocEnd();
          }

          $sHTML.= $oHTML->getBlocEnd();

        $sHTML.= $oHTML->getBlocEnd();
      }

      $sHTML.= $oHTML->getFloatHack();
      $sHTML.= $oHTML->getBlocEnd();

      return $sHTML;
    }


    private function _getDetailUserChart($psDateStart, $psDateEnd, $pasChartData, $psUserName, $pbMerged = false)
    {
      //all is managed in millisec. -86000 to start the chart +-1 day
      $nStart = strtotime($psDateStart)-86400;
      $nEnd = strtotime($psDateEnd)+86400;
      //dump($pasChartData);

      $asChartData = array();
      if($pbMerged)
      {
        //received data from all users, need to merge it in one
        $asMerged = array();
        foreach($pasChartData as $asUserData)
        {
          foreach($asUserData as $nStatus => $asData)
          {
            foreach($asData as $nTimestamp => $asCandidate)
            {
              $this->_getScatterPointData($asChartData, $asCandidate, $nStatus, $nTimestamp);
            }
          }
        }
        $pasChartData = $asMerged;
      }
      else
      {
        //receive data from a single user
        foreach($pasChartData as $nStatus => $asData)
        {
          foreach($asData as $nTimestamp => $asCandidate)
          {
            $this->_getScatterPointData($asChartData, $asCandidate, $nStatus, $nTimestamp);
          }
        }
      }

      //dump($asChartData);


      $sId = uniqid();
      if(!$this->cbInAjax && $this->cnWindowSize > 1)
        $sHTML = '<div id="pipelineChart_'.$sId.'" style="height: 650px; width:100%; margin: 5px 5px 0 0; float: left;"></div>';
      elseif($this->cnHeight)
        $sHTML = '<div id="pipelineChart_'.$sId.'" style="height: '.$this->cnHeight.'px; width:'.$this->cnWidth.'px; margin: 5px 5px 0 0; float: left;"></div>';
      else
        $sHTML = '<div id="pipelineChart_'.$sId.'" style="height: 450px; width: 800px; margin: 5px 5px 0 0; float: left;"></div>';

      //$("#pipelineChart_'.$sId.'").highcharts({
      $sHTML.= '
        <script>
        $(function() {
        var oChart = $("#pipelineChart_'.$sId.'").highcharts("StockChart", {

            rangeSelector: {
              inputEnabled: false,
              selected: 1,
              labelStyle: { display: "none"},
              buttonTheme: { display: "none"}
            },
            navigator: {  height: 30},
            scrollbar: { height: 10},
            title:
            {
              text: "'.$psUserName.'\'s pipeline",
              verticalAlign: "top"
            },
            plotOptions:
            {
              scatter:
              {
                marker:
                {
                  shared: true,
                  radius: 3
                }
              }
            },
            tooltip: {
                 shared: true,
                 pointFormat: "{point.name} <b>{point.candidate}</b>"
            },
            xAxis: {
                type: "datetime",';

      if($this->cbWatercooler)
      {
        //display the full chart
        if($nEnd > time())
          $nEnd = time();

        $nRange = ($nEnd - $nStart)*1000;
        $sHTML.= '
                  labels: { rotation: 45, style: {fontSize: "8px"}},
                  minRange: '.$nRange.',
                  min: '.($nStart*1000).',
                  max: '.($nEnd*1000).',
                  minTickInterval: 43200000 /* 12*3600*1000*/,
                  minorGridLineColor: \'#f2f2f2\' ';
      }
      else
      {
        $sHTML.= '
                  labels: { rotation: 45, style: {fontSize: "8px"}},
                  minRange: 24 * 3600 * 1000,
                  min: '.($nStart*1000).',
                  max: '.($nEnd*1000).',
                  title: {
                      text: null
                  }';
      }


       $sHTML.= '
            },
            yAxis: {
                /*categories: ["Failed", "Pitched", "Resume", "CCM", "CCM1", "CCM2", "CCM3", "Offer", "Placed", "Stalled", "Failed"],
                type: "category",*/
                min: -10,
                max: 30,
                gridLineColor: "#dedede",
                labels:
                {
                  formatter: function()
                  {
                    if(this.value < -4)
                      return "Failed";
                    if(this.value == 0)
                      return "Pitched";
                    if(this.value == 5)
                      return "Resume";
                    if(this.value == 10)
                      return "CCM";
                    if(this.value == 20)
                      return "Offer";
                    if(this.value == 30)
                      return "Placed";

                    return "";
                  }
                }
            },
            series: [';

      foreach($asChartData as $sStatus => $asData)
      {
        $sHTML.= '
           {
                name: "'.$sStatus.'",
                type: "scatter",
                pointInterval: 24 * 3600 * 1000,
                point:
                {
                  events:
                  {
                    click: function(event)
                    {
                      view_position(\'/index.php5?uid=555-005&ppa=ppav&ppt=jd&pg=ajx&ppk=\'+this.options.id);
                    }
                  }
                },
                data: [';

            //$asValues = array();
            ksort($asData);
            foreach($asData as $nDate => $avValue)
            {
              if($avValue)
                $sHTML.= '{x: '.$nDate.', y: '.$avValue[0].', candidate: "'.addslashes($avValue[1]).'", id: "'.$avValue[2].'"},';
                //$asValues[] = '{x: '.$nDate.', y: '.$avValue[0].', candidate: "'.addslashes($avValue[1]).'", id: "'.$avValue[2].'"}';
            }

            $sHTML.= ']
            }, ';
        }

     $sHTML.= ']

        });
      });

      </script>';
      return $sHTML;
    }


    private function _getScatterPointData(&$pasResult, &$pasData, $pnStatus, $pnTimestamp, $psItem = 'candidate')
    {

      $sStatus = $this->casStatus[$pnStatus];

      $nChartStatus = 0;
      switch($pnStatus)
      {
        case 1: $nChartStatus = 0; break;
        case 2: $nChartStatus = 3; break;

        case ($pnStatus >= 50 && $pnStatus < 100):
          $nChartStatus = 10 + ($pnStatus-50) * 0.5; break;

        case 100: $nChartStatus = 20; break;
        case 101: $nChartStatus = 30; break;

        default:
          $nChartStatus = -5; break;
      }

      if($psItem == 'candidate')
      {
        //$nItemPk = (int)$pasData['candidatefk'];
        $nItemPk = (int)$pasData['positionfk'];
        $sItem = 'position #'.$pasData['positionfk'].':<br/>'.$pasData['firstname'].' '.$pasData['lastname'].' #'.$nItemPk;
        //$sUrl = $this->_oPage->getAjaxUrl('555-001', CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, $nCandidatePk);
      }
      else
      {

        $nItemPk = (int)$pasData['positionfk'];
        $sItem = 'position #'.$pasData['positionfk'].' <br />at least 1 candidate with status ['.$sStatus.']';
      }

      $pasResult[$sStatus][$pnTimestamp] = array($nChartStatus, $sItem, $nItemPk);
      return true;
    }


    private function _getKpi($sDateStart, $sDateEnd, $asUser)
    {

      $oLogin = CDependency::getCpLogin();
      $asUserData = $oLogin->getUserData();
      $sPeriod = '';


      //specific settings for KPI / watercooler
      $bAjax = ($this->csMode == 'ajx');
      $sChart = getValue('chart');
      if($bAjax)
      {
        //if manager access all charts
        if(isset($asUserData['group'][103]))
        {
          $asAccessibleChart = array('rev_tokyo', 'rev_split', 'met', 'set_vs_met', 'in_play', 'met_vs_meetings');
        }
        else
        {
          if(isset($asUserData['group'][106]))
            $asAccessibleChart = array('rev_tokyo', 'rev_split', 'met', 'set_vs_met', 'in_play', 'met_vs_meetings');
          else
            $asAccessibleChart = array('met', 'set_vs_met', 'in_play', 'met_vs_meetings');
        }

      }
      else
      {
        //IP address based restriction
        if(in_array($_SERVER['REMOTE_ADDR'], $this->casTokyoIp))
          $asAccessibleChart = array('rev_tokyo', 'rev_split', 'met', 'set_vs_met', 'in_play', 'met_vs_meetings', 'pipeline', 'placement', 'placement_grp', 'placement_loc', 'contrib');
        else
          $asAccessibleChart = array('met', 'set_vs_met', 'in_play', 'met_vs_meetings', 'pipeline', 'placement', 'placement_grp', 'placement_loc', 'contrib');

        //get the display period
        //$sCurrentChart = (int)getValue('chart', 'met');
      }

      if(!empty($sChart))
        $asAccessibleChart = array_intersect(array($sChart), $asAccessibleChart);


      //dump($asAccessibleChart);

      $sPeriod = getValue('period', 'month');
      $nYear = getValue('year', 0);


        switch($sPeriod)
        {
          case 'q':
          case 'q1':
          case 'q2':
          case 'q3':
          case 'q4':

            $nQuarter = (int)preg_replace('/[^0-9]/', '', $sPeriod);
            if(empty($nQuarter))
              $nQuarter = floor((date('m') - 1) / 3) + 1;

            $nStartMonth = ((($nQuarter-1) * 3) + 1);
            $nYear = $this->_getYearWithStat($nStartMonth);
            $sDateStart = $nYear.'-'.$nStartMonth.'-01';

            //need mktime here because q4 -> 2014-13-01 o_O
            $sDateEnd = date('Y-m-d', mktime(0, 0, 0, ((($nQuarter) * 3) + 1), 1, $nYear));
            $sPeriod = ' QUARTER #'.$nQuarter;
            break;

          case 's':
          case 's1':
          case 's2':

            $nSemester = (int)preg_replace('/[^0-9]/', '', $sPeriod);
            if(empty($nSemester))
              $nSemester = floor((date('m') - 1) / 6) + 1;

            if($nSemester == 1)
            {
              $sDateStart = date('Y').'-01-01';
              $sDateEnd = date('Y').'-07-01';
            }
            else
            {
              $nYear = $this->_getYearWithStat(6);
              $sDateStart = $nYear.'-07-01';
              $sDateEnd = ($nYear+1).'-01-01';
            }

            $sPeriod = ' SEMESTER #'.$nSemester;
            break;

          case 'year':
            $sDateStart = date('Y').'-01-01';
            $sDateEnd = (date('Y')+1).'-01-01';

            $sPeriod = ' FULL YEAR ';
            break;

          case 'custom':
            // Placeholder
            break;

          case 'month':
          default:
            $sDateStart = date('Y-m').'-01';
            $sDateEnd = date('Y-m', strtotime('+1 month')).'-01';

            $sPeriod = ' CURRENT MONTH ';
            break;
        }

      $oDateStart = new DateTime($sDateStart);
      $oDateEnd = new DateTime($sDateEnd);
      $oInterval = $oDateEnd->diff($oDateStart);
      $nMonth = ((int)$oInterval->format('%y') * 12) + (int)$oInterval->format('%m') + 1;  //aug to dec ...displays aug and december stats


      $oChart = CDependency::getComponentByName('charts');
      $oChart->includeChartsJs(true);
      $oHTML = CDependency::getCpHtml();
      $sCategories = $this->_getCategories($sDateStart, $sDateEnd);

      $anUser = array_keys($asUser);

      $sHTML = '';


      if(in_array('met', $asAccessibleChart))
      {
        $sHTML.= $oHTML->getTitle('Met candidates&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;Period: '.$sPeriod.'&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;Dates: '.$sDateStart.' to '.$sDateEnd);
        $sHTML.= $this->_getKpiMet($anUser, $sDateStart, $sDateEnd, $nMonth, $sCategories, $bAjax);
      }

      if(in_array('set_vs_met', $asAccessibleChart))
      {
        $sHTML.= $oHTML->getTitle('Set vs Met: '.$sPeriod.' -- From: '.$sDateStart.' to '.$sDateEnd);
        $sHTML.= $this->_getKpiSetVsMet($anUser, $sDateStart, $sDateEnd, $nMonth, $sCategories, $bAjax);
      }

      if(in_array('in_play', $asAccessibleChart))
      {
        $sHTML.= $oHTML->getTitle('In play: '.$sPeriod.' -- From: '.$sDateStart.' to '.$sDateEnd);
        $sHTML.= $this->_getKpiInPlay($anUser, $sDateStart, $sDateEnd, $nMonth, $sCategories, $bAjax);
      }

      if(in_array('pipeline', $asAccessibleChart))
      {
        $asChartData = $this->_getModel()->getPiplelineDetailData($anUser, $sDateStart, $sDateEnd, 250);
        $sHTML.= $this->_getDetailUserChart($sDateStart, $sDateEnd, $asChartData, 'All users', true);
      }

      $sLocation = getValue('location');
      if($sLocation == 'all')
        $sLocationLabel = 'All locations';
      else
        $sLocationLabel = $sLocation;

      if(in_array('placement', $asAccessibleChart))
      {
        $asChartData = $this->_getModel()->getPlacementData($anUser, $sDateStart, $sDateEnd, $sLocation);
        $sHTML.= $this->_getPlacementChart($sDateStart, $sDateEnd, $asChartData, 'SCKK ('.ucfirst($sLocationLabel).') - Individual Revenue Leaders', 0);
      }

      if(in_array('placement_grp', $asAccessibleChart))
      {
        $asChartData = $this->_getModel()->getPlacementData($anUser, $sDateStart, $sDateEnd, $sLocation, 'team');
        $sHTML.= $this->_getPlacementChart($sDateStart, $sDateEnd, $asChartData, 'Placement per team ('.ucfirst($sLocationLabel).') - Slate group', 1);
      }

      if(in_array('placement_loc', $asAccessibleChart))
      {
        $asChartData = $this->_getModel()->getPlacementData($anUser, $sDateStart, $sDateEnd, 'all', 'location');
        $sHTML.= $this->_getPlacementChart($sDateStart, $sDateEnd, $asChartData, 'Placement per location ('.ucfirst($sLocationLabel).') - Slate group', 2);
      }


      if(in_array('contrib', $asAccessibleChart))
      {
        $asChartData = $this->_getModel()->getContributorData($anUser, $sDateStart, $sDateEnd, 'all', 'location');
        $sHTML.= $this->_getContributorChart($sDateStart, $sDateEnd, $asChartData, 'Contibutions to placements ('.ucfirst($sLocationLabel).') ', 2);
      }

      //https://slistem.devserv.com/index.php5?uid=555-006&ppa=ppav&ppt=&ppk=0&chart_only=1&pg=ajx
      $sURL = $this->_oPage->getRequestedUrl().'&pg=';
      $sHTML.= $this->_oDisplay->getText('<br />View in full page', array('style' => 'font-size: 11px;'));
      $sHTML.= '&nbsp;&nbsp;'.$this->_oDisplay->getLink(' All ', $sURL.'&location=all', array('target' => '_blank'));
      $sHTML.= '&nbsp;&nbsp;'.$this->_oDisplay->getLink(' Tokyo ', $sURL.'&location=tokyo', array('target' => '_blank'));
      $sHTML.= '&nbsp;&nbsp;'.$this->_oDisplay->getLink(' Manila ', $sURL.'&location=manila', array('target' => '_blank'));
      $sHTML.= '&nbsp;&nbsp;'.$this->_oDisplay->getLink(' Canada ', $sURL.'&location=canada', array('target' => '_blank'));
      $sHTML.= '&nbsp;&nbsp;'.$this->_oDisplay->getLink(' Hong Kong ', $sURL.'&location=hongkong', array('target' => '_blank'));
      $sHTML.= '&nbsp;&nbsp;'.$this->_oDisplay->getLink(' Singapore ', $sURL.'&location=singapore', array('target' => '_blank'));

      return $sHTML;
    }

    private function _getYearWithStat($pnMonthStart)
    {
      if($pnMonthStart > date('m'))
        return date('Y') - 1;

      return date('Y');
    }

    private function _getTargetToDate($pnTarget, $psDateStart, $psDateEnd)
    {
      $sKey = $pnTarget.'_'.$psDateStart.'_'.$psDateEnd;
      if(isset($this->casTmpTarget[$sKey]))
        return $this->casTmpTarget[$sKey];

      $nThisMonth = date('m');
      // $nThisyear = date('Y');

      $oDateNow = new DateTime();

      //FDM: first day of the month
      $oDateFDM = new DateTime(date('Y-m-01'));
      $oDateStart = new DateTime($psDateStart);

      if($nThisMonth < date('m', strtotime($psDateEnd)))
        $oDateEnd = new DateTime(date('Y-m-t'));
      else
        $oDateEnd = new DateTime($psDateEnd);

      $oInterval = $oDateStart->diff($oDateFDM);
      $nMonth = (int)$oInterval->format('%m');

      $month_total_days = $oDateEnd->format('t');
      $target_per_day = $pnTarget / $month_total_days;

      $oInterval = $oDateFDM->diff($oDateEnd);
      $nDayThisMonth = (int)$oInterval->format('%d');
      if(empty($nDayThisMonth))
        $nDayThisMonth = 1;


      $oInterval = $oDateFDM->diff($oDateNow);
      $nCurrentDay = (int)$oInterval->format('%d');


      if(empty($nCurrentDay) || (empty($nMonth) && $nDayThisMonth == $nCurrentDay))
        return $pnTarget;

      $this->casTmpTarget[$sKey] = round( ($pnTarget * $nMonth) + ($target_per_day * $nCurrentDay), 1, PHP_ROUND_HALF_DOWN);
      return $this->casTmpTarget[$sKey];
    }


    private function _getKpiMet($panUser, $psDateStart, $psDateEnd, $pnMonth, $psCategories, $pbAjax)
    {
      // --------------------------------------------------
      //Time to merge all the different stats for each user
      $group_name = strtolower(getValue('group_name', 'researcher'));

      $asData = $this->_getModel()->getSicChartMet($panUser, $psDateStart, $psDateEnd, $group_name);
      $asChartData = array();
      $nUser = count($panUser) - 2;
      $nCount = 0;
      foreach($panUser as $nLoginPk)
      {
        if(isset($asData[$nLoginPk]))
          $asStat = $asData[$nLoginPk];
        else
          $asStat = array();

        $sUserName = $this->casAllUserData[$nLoginPk]['pseudo'];

        $asChartData['target2'][$sUserName] = 20;
        $asChartData['target1'][$sUserName] = $this->_getTargetToDate($asChartData['target2'][$sUserName], $psDateStart, $psDateEnd);
        $asChartData['met'][$sUserName] = 0;

        foreach($asStat as $anMet)
        {
          if(isset($anMet[1]))
            $asChartData['met'][$sUserName]+= $anMet[1];
        }

        //add a picture on the last marker
        if($nCount == $nUser)
          $asChartData['target1'][$sUserName] = '{y: '.$asChartData['target1'][$sUserName].', marker: {symbol: "url('.CONST_CRM_DOMAIN.'/component/sl_stat/resources/pictures/target_to_date2.gif)"}}';
        else
          $asChartData['target1'][$sUserName] = '{y: '.$asChartData['target1'][$sUserName].', marker: {enabled: false}}';

        $nCount++;
      }
      // arsort($asChartData['met']);

      /*$nMiddle = floor(count($asChartData['target1'])/2);
      $nCount = 0;
      dump($nMiddle);
      foreach($asChartData['target1'] as $sUserName => $nValue)
      {
        if($nCount == $nMiddle)
          $asChartData['target1'][$sUserName] = '{y: '.$nValue.', marker: {symbol: "url('.CONST_CRM_DOMAIN.'/component/sl_stat/resources/pictures/target_to_date.gif)"}}';

        $nCount++;
      }*/

      if($this->cbWatercooler)
      {
        $sLabelOption = ',labels: { rotation: -45 } ';
      }
      else
        $sLabelOption = '';


      $sId = uniqid();
      $sHTML = '<br />';

      if($pbAjax)
        $sHTML.= '<div id="sicChart_'.$sId.'" style="height: 250px; width: 780px;  margin: 0 auto;"></div>';
      else
        $sHTML.= '<div id="sicChart_'.$sId.'" style="min-height: 750px; width: 100%;  margin: 0 auto;"></div>';

      $sHTML.= '<script>
        $(function ()
        {
          $("#sicChart_'.$sId.'").highcharts(
          {
            chart:
            {
              events :
              {
                load : edgeExtend,
                redraw : edgeExtend
              }
            },
            title: {
                text: ""
            }';

        if($this->cbDisplayLegend)
        {
          $sHTML.= ',
            legend: {
                layout: "vertical",
                align: "right",
                verticalAlign: "middle",
                borderWidth: 0
            }';
        }

        $sHTML.= ',
            plotOptions:
            {
              spline:
              {
                marker: { enabled: true },
                lineWidth: 1,
                states: {hover: {lineWidth: 2 }}

              },
              line:
              {
                marker: { enabled: false },
                lineWidth: 1,
                states: {hover: {lineWidth: 2 }}
              }
            },
            xAxis: {
              categories: ["'.implode('","', array_keys($asChartData['met'])).'"]
              '.$sLabelOption.'
            },
            yAxis:
            {
                title:
                {
                  text: "Candidates"
                }
            },
            tooltip: {
              shared: true,
              useHTML: true,
              formatter: function()
              {
                var s = "<div class=\'chartTooltip\'>"+ this.x +"</div>";

                $.each(this.points, function(i, point)
                {
                  s += "<span style=\'color:"+point.series.color+"\'>" + point.series.name +"</span>: " + point.y+"<br/>";
                });
                return s;
              }
            },
            series: [
            {
                type: "line",
                color: "#D15727",
                name: "Monthly target",
                data: ['.implode(',', $asChartData['target2']).'],
                shadow: false
            },
            {
                type: "spline",
                color: "#EDAB55",
                name: "Target to date",
                data: ['.implode(',', $asChartData['target1']).'],
                shadow: false
            },
            {
              type: "column",
              name: "Met candidates",
              data: ['.implode(',', $asChartData['met']).'],
              color: "#5485B9",
              dataLabels:
              {
                enabled: true,
                /*rotation: -90,*/
                style:
                {
                  color: "#FFFFFF",
                  fontWeight: "bold",
                  fontSize: 20
                },
                align: "center",
                x: 0,
                y: 20
              }
             }
           ]
        });
      });
      </script>';

     return $sHTML;
    }


    private function _getKpiSetVsMet($panUser, $psDateStart, $psDateEnd, $pnMonth, $psCategories, $pbAjax)
    {
      // --------------------------------------------------
      //Time to merge all the different stats for each user
      $group_name = strtolower(getValue('group_name', 'researcher'));

      $asData = $this->_getModel()->getKpiSetVsMet($panUser, $psDateStart, $psDateEnd, $group_name);
      $asChartData = array();

      foreach($panUser as $nLoginPk)
      {
        if(isset($asData[$nLoginPk]))
          $asStat = $asData[$nLoginPk];
        else
          $asStat = array('set' => 0, 'met' => 0);

        $sUserName = $this->casAllUserData[$nLoginPk]['pseudo'];


        $asChartData['met'][$sUserName] = $asStat['met'];

        $asChartData['set'][$sUserName] = $asStat['set'] - $asStat['met'];

        if ($asChartData['set'][$sUserName] < 0)
          $asChartData['set'][$sUserName] = 0;

        $asChartData['total'][$sUserName] = $asChartData['met'][$sUserName] + $asChartData['set'][$sUserName];
      }

      // array_multisort($asChartData['total'], SORT_DESC, SORT_NUMERIC, $asChartData['met'], $asChartData['set']);

      $sData = '{name: "Meeting set", color: "#5485B9", data: ['.implode(',', $asChartData['set']).'], stack: "meeting",
        dataLabels:{
          formatter: function()
          {
            if(this.y == 0)
              return "";

            return this.y;
          }
        }
      }';

      $sData .= ', {name: "Candidates met", color: "#F7B94F", data: ['.implode(',', $asChartData['met']).'], stack: "meeting",
        dataLabels:{
          formatter: function()
          {
            if(this.y == 0)
              return "";

            return this.y;
          }
        }
      }';


      $sId = uniqid();
      $sHTML = '<br />';

      if($pbAjax)
        $sHTML.= '<div id="sicChart_'.$sId.'" style="height: 250px; width: 780px;  margin: 0 auto;"></div>';
      else
        $sHTML.= '<div id="sicChart_'.$sId.'" style="min-height: 750px; width: 100%;  margin: 0 auto;"></div>';

      if($this->cbWatercooler)
      {
        $sLabelOption = ',labels: { rotation: -45 } ';
      }
      else
        $sLabelOption = '';


      $sHTML.= '<script>
        $(function()
        {
          $("#sicChart_'.$sId.'").highcharts(
          {
            chart:
            {
              type: "column"
            },
            title: {
                text: ""
            },';

        if($this->cbDisplayLegend)
        {
          $sHTML.= '
            legend: {
                layout: "vertical",
                align: "right",
                verticalAlign: "middle",
                borderWidth: 0
            },';
        }

        $sHTML.= '
            plotOptions:
            {
              column:
              {
                stacking: "normal",
                dataLabels:
                {
                  enabled: true,
                  color: "white",
                  style:
                  {
                    textShadow: "0 0 3px black, 0 0 3px black",
                    fontSize: 20,
                    fontWeight: "bold"
                  },
                  useHtml: true,
                  formatter: function(oPoint)
                  {
                    if(this.y)
                      return this.y;

                    return "";
                  }
                }
              }
            },
            xAxis: {
              categories: ["'.implode('","', array_keys($asChartData['set'])).'"]
              '.$sLabelOption.'
            },
            yAxis:
            {
                title:
                {
                  text: "Meetings"
                },
                stackLabels:
                {
                    enabled: true,
                    style: { fontWeight: "bold", color: (Highcharts.theme && Highcharts.theme.textColor) || "gray" }
                }
            },
            tooltip: {
              shared: true,
              useHTML: true,
              formatter: function()
              {
                var s = "<div class=\'chartTooltip\'>"+ this.x +"</div>";

                $.each(this.points, function(i, point)
                {
                  s += "<span style=\'color:"+point.series.color+"\'>" + point.series.name +"</span>: " + point.y+"<br/>";
                });
                return s;
              }
            },
            series: ['.$sData.']
        });
      });
      </script>';

     return $sHTML;
    }


    private function _getKpiInPlay($panUser, $psDateStart, $psDateEnd, $pnMonth, $psCategories, $pbAjax)
    {
      // --------------------------------------------------
      //Time to merge all the different stats for each user
      $asData = $this->_getModel()->getKpiInPlay($panUser, $psDateStart, $psDateEnd);
      //dump($asData);

      $asChartData = array();
      $nUser = count($panUser) - 2;
      $nCount = 0;
      foreach($panUser as $nLoginPk)
      {
        $sUserName = $this->casAllUserData[$nLoginPk]['pseudo'];
        $asChartData['target2'][$sUserName] = 7;
        $asChartData['target1'][$sUserName] = $this->_getTargetToDate($asChartData['target2'][$sUserName], $psDateStart, $psDateEnd);

        if(!isset($asData[$nLoginPk]))
          $asChartData['play'][$sUserName] = 0;
        else
          $asChartData['play'][$sUserName] = $asData[$nLoginPk];

        //add a picture on the last marker
        if($nCount == $nUser)
          $asChartData['target1'][$sUserName] = '{y: '.$asChartData['target1'][$sUserName].', marker: {symbol: "url('.CONST_CRM_DOMAIN.'/component/sl_stat/resources/pictures/target_to_date2.gif)"}}';
        else
          $asChartData['target1'][$sUserName] = '{y: '.$asChartData['target1'][$sUserName].', marker: {enabled: false}}';

        $nCount++;
      }
      // arsort($asChartData['play']);

      $sId = uniqid();
      $sHTML = '<br />';

      if($pbAjax)
        $sHTML.= '<div id="sicChart_'.$sId.'" style="height: 250px; width: 780px;  margin: 0 auto;"></div>';
      else
        $sHTML.= '<div id="sicChart_'.$sId.'" style="min-height: 750px; width: 100%;  margin: 0 auto;"></div>';


      if($this->cbWatercooler)
      {
        $sLabelOption = ',labels: { rotation: -45 } ';
      }
      else
        $sLabelOption = '';

      $sHTML.= '<script>
        $(function()
        {
          $("#sicChart_'.$sId.'").highcharts(
          {
            chart:
            {
              events :
              {
                load : edgeExtend,
                redraw : edgeExtend
              }/*,
              plotShadow: false nope*/
            },
            title: {
                text: ""
            }';

        if($this->cbDisplayLegend)
        {
          $sHTML.= ',
            legend: {
                layout: "vertical",
                align: "right",
                verticalAlign: "middle",
                borderWidth: 0
            }';
        }

        $sHTML.= ',
            plotOptions:
            {
              spline:
              {
                marker: { enabled: true  },
                lineWidth: 1
              },
              line:
              {
                marker: { enabled: false },
                lineWidth: 1,
                states: {hover: {lineWidth: 2 }}

              }
            },
            xAxis: {
              categories: ["'.implode('","', array_keys($asChartData['play'])).'"]
                '.$sLabelOption.'
            },
            yAxis:
            {
                title:
                {
                  text: "Candidates"
                }
            },
            tooltip: {
              shared: true,
              useHTML: true,
              formatter: function()
              {
                var s = "<div class=\'chartTooltip\'>"+ this.x +"</div>";

                $.each(this.points, function(i, point)
                {
                  s += "<span style=\'color:"+point.series.color+"\'>" + point.series.name +"</span>: " + point.y+"<br/>";
                });
                return s;
              }
            },
            series: [
            {
                type: "line",
                color: "#D15727",
                name: "Monthly target",
                data: ['.implode(',', $asChartData['target2']).'],
                shadow: false
            },
            {
                type: "spline",
                color: "#EDAB55",
                name: "Target to date",
                data: ['.implode(',', $asChartData['target1']).'],
                shadow: false
            },';


       $sHTML.= '
            {
              type: "column",
              name: "In play candidates",
              data: ['.implode(',', $asChartData['play']).'],
              color: "#5485B9",
              dataLabels:
              {
                enabled: true,
                /*rotation: -90,*/
                style:
                {
                  color: "#FFFFFF",
                  fontWeight: "bold",
                  fontSize: 20
                },
                align: "center",
                x: 0,
                y: 20
              }
             }
           ]
        });
      });
      </script>';

     return $sHTML;
    }



    private function _getPlacementChart($psDateStart, $psDateEnd, $pasChartData, $psTitle, $pnMergeResult = 0)
    {
      //dump($pasChartData);
      $oLogin = CDependency::getCpLogin();
      $asNationality = getNationality();
      $asGroups = $oLogin->getGroupList();
      $asGroups[0]['title'] = '<em> - not defined - </em>';

      $asLocation = array('' => '<em> - not defined - </em>',  'tok' => 'SCKK Tokyo', 'man' => 'SGHC Manila', 'can' => 'SGHC Canada', 'hon' => 'SGL Hong kong', 'sin' => 'SGHC Singapore');


      $nCount = 1;
      $anTotal = array('signed' => 0, 'paid' => 0, 'placed' => 0);
      foreach($pasChartData as $nKey => $asUserData)
      {
        //dump($asUserData);
        $nNationality = $this->casAllUserData[(int)$asUserData['loginfk']]['nationalityfk'];
        if(empty($nNationality) || !isset($asNationality[$nNationality]))
          $sFlagPic = 'world_48.png';
        else
          $sFlagPic = $asNationality[$nNationality].'_48.png';


        $pasChartData[$nKey]['rank'] = $nCount;

        if(empty($pnMergeResult))
        {
          $pasChartData[$nKey]['consultant'] = '<div class="revenue_cons">'.$oLogin->getUserLink((int)$asUserData['loginfk']).' '.$asUserData['loginfk'];
          $pasChartData[$nKey]['consultant'].= $this->_oDisplay->getPicture('/common/pictures/flags/'.$sFlagPic).'</div>';
          $pasChartData[$nKey]['team'] = @$asGroups[(int)$asUserData['groupfk']]['title'];
        }
        elseif($pnMergeResult == 1)
        {
          $pasChartData[$nKey]['consultant'] = '<div class="revenue_cons">'.@$asGroups[(int)$asUserData['groupfk']]['title'].'</div>';
          $pasChartData[$nKey]['team'] = ' - ';
        }
        else
        {
          $pasChartData[$nKey]['consultant'] = '<div class="revenue_cons">'.@$asLocation[$asUserData['location']].'</div>';
          $pasChartData[$nKey]['team'] = ' - ';
        }


        /*search the user country...
         * $vKey = array_search((int)$asUserData['loginfk'], $anTotal)
         */

        $pasChartData[$nKey]['revenue_signed'] = '&yen;'.number_format($asUserData['revenue_signed'] , 0, '.', ',');
        $pasChartData[$nKey]['revenue_paid'] = '&yen;'.number_format($asUserData['revenue_paid'] , 0, '.', ',');

        $anTotal['signed']+= (int)$asUserData['revenue_signed'];
        $anTotal['paid']+= (int)$asUserData['revenue_paid'];
        $anTotal['placed']+= (int)$asUserData['revenue_placed'];
        /*dump($asUserData);
        dump((int)$asUserData['placed']);*/

        $nCount++;
      }

      //display an extra line for total
      $pasChartData[] = array('rank' => '', 'consultant' => 'Total',
          'revenue_signed' => '&yen;'.number_format($anTotal['signed'], 0, '.', ','),
          'revenue_paid' => '&yen;'.number_format($anTotal['paid'], 0, '.', ','),
          'team' => '',
          'revenue_placed' => $anTotal['placed']
          );



      $sHTML = $this->_oDisplay->getFloathack();
      $sHTML.= $this->_oDisplay->getBlocStart('', array('class' => 'kpi_revenue'));

        $sHTML.= $this->_oDisplay->getBlocStart('', array('class' => 'title'));
        $sHTML.= $psTitle.' - '.$psDateStart.' to '.$psDateEnd;
        $sHTML.= $this->_oDisplay->getBlocEnd();

        //initialize the template
        $asParam = array('sub_template' => array('CTemplateList' => array(0 => array('row' => 'CTemplateRow'))));
        $oTemplate = $this->_oDisplay->getTemplate('CTemplateList', $asParam);

        //get the config object for a specific template (contains default value so it works without config)
        $oConf = $oTemplate->getTemplateConfig('CTemplateList');
        $oConf->setRenderingOption('full', 'full', 'full');

        $oConf->setPagerTop(false);
        $oConf->setPagerBottom(false);

        $oConf->addColumn('Rank', 'rank', array('width' => '5%'));
        $oConf->addColumn('Name', 'consultant', array('width' => '30%'));
        $oConf->addColumn('Signed revenue', 'revenue_signed', array('width' => '20%'));
        $oConf->addColumn('Paid revenue', 'revenue_paid', array('width' => '20%'));
        $oConf->addColumn('Team.', 'team', array('width' => '15%'));
        $oConf->addColumn('Placed', 'revenue_placed', array('width' => '5%'));


        $sHTML.= $oTemplate->getDisplay($pasChartData);

      $sHTML.= $this->_oDisplay->getBlocEnd();
      return $sHTML;
    }


    private function _getUserHomeChart()
    {
      $login_obj = CDependency::getCpLogin();
      $users = $login_obj->getUserList(0, true, true);
      $user_ids = array_keys($users);

      $chart_array = array();
      $start_date = date('Y-m-d', mktime(0, 0, 0, date('m')-2, 1, date('Y')));
      $far_start_date = date('Y-m-d', mktime(0, 0, 0, date('m')-6, 1, date('Y')));
      $end_date = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d')+1, date('Y')));

      $this->_setCustomSize(240, 450);

      $GLOBALS['redis']->set('play_researcher', '');
      $GLOBALS['redis']->set('play_consultant', '');
      $GLOBALS['redis']->set('met_researcher', '');
      $GLOBALS['redis']->set('met_consultant', '');

      foreach($users as $user => $user_data)
      {
        $user_groups = $login_obj->getUserGroup($user);

        $group_name = 'researcher';

        if (isset($user_groups[108]))
          $group_name = 'consultant';

        $chart_array = $this->_getSicStat($start_date, $end_date, array('user_data' => array($user => $user_data),
          'user_id' => $user, 'user_ids' => $user_ids), 0, 'column', false, $group_name);

        // $chart_array['pipeline'] = $this->_getPipeline($sFarStart, $sEnd, array($nUser => $asUserData), 0, 'pie', true);

        if(!empty($chart_array))
        {
          //all charts are generated... save it
          echo 'saving charts for '.$user.'<br />';
          foreach($chart_array as $chart_type => $html)
          {
            $file_obj = fopen(CONST_PATH_ROOT.CONST_PATH_UPLOAD_DIR.'/sl_stat/charts/'.$user.'_'.$chart_type.'.html', 'w+');
            if($file_obj)
            {
              fputs($file_obj, $html);
              fclose($file_obj);
            }
          }
        }

      }

      $GLOBALS['redis']->delete('play_researcher', 'play_consultant', 'met_researcher', 'met_consultant');

    }


    private function _getPositionPipeline($psDateStart, $psDateEnd, $asUser, $pnGroup)
    {
      /* same as the previous with positions status
       *
       * Each position is freflected on the chart by the highest in play candidate status
       *
       */

      $oChart = CDependency::getComponentByName('charts');
      $oChart->includeChartsJs(false, false);
      $oHTML = CDependency::getCpHtml();
      //$anStatus = array_keys($this->casStatus);

      $anUser = array_keys($asUser);
      $asChartData = $this->_getModel()->getPositionPipeData($anUser, $psDateStart, $psDateEnd, 250);
      //dump($asChartData);
      //exit();

      $sHTML = $oHTML->getBlocStart('', array('class' => 'pipe_detail_container'));
      $sHTML.= $oHTML->getTitle('Position pipeline s', 'h3', true);




      //all is managed in millisec. -86000 to start the chart +-1 day
      $nStart = strtotime($psDateStart)-86400;
      $nEnd = strtotime($psDateEnd)+86400;
      //dump($asChartData);
      $asFormatData = array();
      foreach($asChartData as $nStatus => $asData)
      {
        foreach($asData as $nTimestamp => $asPosition)
        {
            $this->_getScatterPointData($asFormatData, $asPosition, $nStatus, $nTimestamp, 'position');
        }
      }


      $sId = uniqid();
      if(!$this->cbInAjax && $this->cnWindowSize > 1)
        $sHTML = '<div id="pipelineChart_'.$sId.'" style="height: 650px; width:100%; margin: 5px 5px 0 0; float: left;"></div>';
      elseif($this->cnHeight)
        $sHTML = '<div id="pipelineChart_'.$sId.'" style="height: '.$this->cnHeight.'px; width:'.$this->cnWidth.'px; margin: 5px 5px 0 0; float: left;"></div>';
      else
        $sHTML = '<div id="pipelineChart_'.$sId.'" style="height: 450px; width: 800px; margin: 5px 5px 0 0; float: left;"></div>';

      //$("#pipelineChart_'.$sId.'").highcharts({
      $sHTML.= '
        <script>
        $(function() {
        var oChart = $("#pipelineChart_'.$sId.'").highcharts("StockChart", {

            rangeSelector: {
              inputEnabled: false,
              selected: 1,
              labelStyle: { display: "none"},
              buttonTheme: { display: "none"}
            },
            navigator: {  height: 30},
            scrollbar: { height: 10},
            title:
            {
              text: "Position pipeline",
              verticalAlign: "top"
            },
            plotOptions:
            {
              scatter:
              {
                marker:
                {
                  shared: true,
                  radius: 4
                }
              }
            },
            tooltip: {
                 shared: true,
                 pointFormat: "{point.name} <b>{point.candidate}</b>"
            },
            xAxis: {
                type: "datetime",
                labels: { rotation: 45, style: {fontSize: "8px"}},
                minRange: 24 * 3600 * 1000,
                min: '.($nStart*1000).',
                max: '.($nEnd*1000).',
                title: {
                    text: null
                }
            },
            yAxis: {
                /*categories: ["Failed", "Pitched", "Resume", "CCM", "CCM1", "CCM2", "CCM3", "Offer", "Placed", "Stalled", "Failed"],
                type: "category",*/
                min: -10,
                max: 30,
                gridLineColor: "#dedede",
                labels:
                {
                  formatter: function()
                  {
                    if(this.value < -4)
                      return "Failed";
                    if(this.value == 0)
                      return "Pitched";
                    if(this.value == 5)
                      return "Resume";
                    if(this.value == 10)
                      return "CCM";
                    if(this.value == 20)
                      return "Offer";
                    if(this.value == 30)
                      return "Placed";

                    return "";
                  }
                }
            },
            series: [';

      foreach($asFormatData as $sStatus => $asData)
      {
        $sHTML.= '
           {
                name: "'.$sStatus.'",
                type: "scatter",
                pointInterval: 24 * 3600 * 1000,
                point:
                {
                  events:
                  {
                    click: function(event)
                    {
                      view_position(\'/index.php5?uid=555-005&ppa=ppav&ppt=jd&pg=ajx&ppk=\'+this.options.id);
                    }
                  }
                },
                data: [';

            //$asValues = array();
            ksort($asData);
            foreach($asData as $nDate => $avValue)
            {
              if($avValue)
                $sHTML.= '{x: '.$nDate.', y: '.$avValue[0].', candidate: "'.addslashes($avValue[1]).'", id: "'.$avValue[2].'"},';
                //$asValues[] = '{x: '.$nDate.', y: '.$avValue[0].', candidate: "'.addslashes($avValue[1]).'", id: "'.$avValue[2].'"}';
            }

            $sHTML.= ']
            }, ';
        }

     $sHTML.= ']

        });
      });

      </script>';
      return $sHTML;





      $sHTML.= $oHTML->getFloatHack();
      $sHTML.= $oHTML->getCR(2);

      $sHTML.= $oHTML->getFloatHack();
      $sHTML.= $oHTML->getBlocEnd();

      return $sHTML;
    }



    private function _getAnalystPage()
    {

      $asUrlOption = array();
      $sDateStart = date('Y-m').'-01';
      $sDateEnd = date('Y-m', strtotime('+1 month')).'-01';
      $sSideMax = '300';
      $sMainMax = '1000';

      $oLogin = CDependency::getCpLogin();


      $sLoginfk = getValue('loginfk');
      if(empty($sLoginfk))
        $asLoginFk = array($oLogin->getUserPk());
      else
        $asLoginFk = explode(',', $sLoginfk);


      $this->_oPage->addCssFile($this->getResourcePath().'/css/sl_stat.css');

      $sHTML = $this->_oDisplay->getBlocStart('', array('class' => 'statChartContainer'));

      $sHTML.= $this->_oDisplay->getTitle('Analyst stat', 'h3', true);
      $sHTML.= $this->_oDisplay->getCR();


      //---------------------------------------------------------------------------
      //---------------------------------------------------------------------------
      //start creating the page
      //split the page in 2:

      //left section containing filtering form
      $sHTML.= $this->_oDisplay->getBlocStart('', array('class' => 'statPageSectionLeft', 'style' => 'width: 20%; min-width: 230px; max-width: '.$sSideMax.'px; '));

        $sURL = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_REFRESH, 'analyst', 0, $asUrlOption);
        $oForm = $this->_oDisplay->initForm('statForm');
        $oForm->setFormParams('statForm', true, array('action' => $sURL, 'ajaxTarget' => 'statPageSectionRight',  'submitLabel'=>'Get stat!', 'noCancelButton' => true));


        $oForm->addField('select', 'period', array('label' => 'Display stats for', 'onchange' =>
          '
          var vValue = $(this).val();
          if(vValue == \'custom\')
          {
            $(this).closest(\'form\').find(\'.date_selector\').removeClass(\'hidden\');
          }
          else
          {
            $(this).closest(\'form\').find(\'.date_selector\').addClass(\'hidden\');
          }'));
        $oForm->setFieldDisplayParams('period', array('class' => 'period_selector hidden'));
        $oForm->addOption('period', array('label' => 'This month', 'value' => 'month'));
        $oForm->addOption('period', array('label' => 'Q1 '.date('Y'), 'value' => 'q1'));
        $oForm->addOption('period', array('label' => 'Q2 '.$this->_getYearWithStat(3), 'value' => 'q2'));
        $oForm->addOption('period', array('label' => 'Q3 '.$this->_getYearWithStat(6), 'value' => 'q3'));
        $oForm->addOption('period', array('label' => 'Q4 '.$this->_getYearWithStat(9), 'value' => 'q4'));
        $oForm->addOption('period', array('label' => 'S1 '.date('Y'), 'value' => 's1'));
        $oForm->addOption('period', array('label' => 'S2 '.$this->_getYearWithStat(6), 'value' => 's2'));
        $oForm->addOption('period', array('label' => 'All year', 'value' => 'year'));
        $oForm->addOption('period', array('label' => 'Custom dates', 'value' => 'custom'));


        $oForm->addField('input', 'date_start', array('type' => 'month', 'label' => 'From', 'value' => $sDateStart));
        $oForm->setFieldDisplayParams('date_start', array('class' => 'date_selector'));

        $oForm->addField('input', 'date_end', array('type' => 'month', 'label' => 'To', 'value' => $sDateEnd));
        $oForm->setFieldDisplayParams('date_end', array('class' => 'date_selector'));


        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        $oForm->addField('misc', '', array('type' => 'text', 'text' => '<div class="separator"></div>'));

        $asUser = $oLogin->getUserByTeam(114, '', false);
        $bFoundAnalyst = false;

        $oForm->addField('select', 'loginfk[]', array('label' => 'Analysts', 'url' => $sURL));


        foreach($asUser as $nUserPk => $asUserData)
        {
          if($asUserData['status'] >= 1)
            $sGroup = 'Active analysts';
          else
            $sGroup = 'Inactive analysts';

          if(in_array($nUserPk, $asLoginFk))
          {
            $bFoundAnalyst = true;
            $oForm->addOption('loginfk[]', array('label' => $asUserData['firstname'].' '.$asUserData['lastname'], 'value' => $nUserPk, 'group' => $sGroup, 'selected' => 'selected'));
          }
          else
            $oForm->addOption('loginfk[]', array('label' => $asUserData['firstname'].' '.$asUserData['lastname'], 'value' => $nUserPk, 'group' => $sGroup));
        }

        $sHTML.= $oForm->getDisplay();
      $sHTML.= $this->_oDisplay->getBlocEnd();


      if(!$bFoundAnalyst)
        $asLoginFk = array();

      $sHTML.= $this->_oDisplay->getBlocStart('statPageSectionRight', array('class' => 'statPageSectionRight', 'style' => ' max-width: '.$sMainMax.'px;'));
      $sHTML.= $this->_getAnalystStat($sDateStart, $sDateEnd, $asLoginFk);
      $sHTML.= $this->_oDisplay->getBlocEnd();

      $sHTML.= $this->_oDisplay->getFloatHack();
      $sHTML.= $this->_oDisplay->getBlocEnd();

      return $sHTML;
    }


    private function _getAnalystStat($psDateStart = '', $psDateEnd = '', $pasLoginFk = array())
    {
      if(empty($psDateStart))
      {
        $psDateStart = getValue('date_start');
        $psDateEnd = getValue('date_end');
        $pasLoginFk = @$_POST['loginfk'];
      }

      if(empty($psDateStart) || empty($psDateEnd) || empty($pasLoginFk))
        return $this->_oDisplay->getBlocMessage('Please choose an analyst and dates to display stats');

      //$oCandidate = CDependency::getComponentByUid('555-001');
      //$asCandidates = $this->_getModel()->getAnalystCandidatesSummary($pasLoginFk, $psDateStart, $psDateEnd);


      $oLogin = CDependency::getCpLogin();
      $oSlateVars = CDependency::getComponentByUid('555-001')->getVars();
      $nUser = (int)$pasLoginFk[0];
      $sUser = strip_tags($oLogin->getUserLink($nUser));


      $sHTML = $this->_oDisplay->getTitle('Candidate summary&nbsp;&nbsp;&nbsp;&nbsp; ['.$psDateStart.' - '.$psDateEnd.']', 'h3', true);

      $asMainData = $this->_getModel()->getAnalystPieData($pasLoginFk, $psDateStart, $psDateEnd, 251);
      if(!empty($asMainData))
      {
        $asMainPieData = array();
        if(!empty($asMainData))
        {
          foreach($asMainData as $asStatData)
          {
            foreach($asStatData as $nStatus => $nNumber)
            {
              if(!isset($asMainPieData[$nStatus]))
                $asMainPieData[$nStatus] = 0;

              $asMainPieData[$nStatus]+= $nNumber;
            }
          }
          arsort($asMainPieData);
        }

        $sData = '';
        foreach($asMainPieData as $sKey => $nValue)
        {
          $sStatus = $this->casStatus[$sKey];
          if(empty($sData))
          {
            //detach the first slice
            $sData.= '{name: "'.$sStatus.'", y: '.$nValue.', color: "'.$this->casColor[$sStatus].'", sliced: true },';
          }
          else
            $sData.= '{name: "'.$sStatus.'", y: '.$nValue.', color: "'.$this->casColor[$sStatus].'", sliced: false },';

        }


        $sId = uniqid();
        $sHTML.= '
              <div id="pipelineChart_'.$sId.'" style="height: 300px; width: 550px; margin: 0 auto;"></div><script>
              $(function () {
              $("#pipelineChart_'.$sId.'").highcharts({
                  legend:
                  {
                    layout: "vertical",
                    align: "right",
                    verticalAlign: "top",
                    x: 0,
                    y: -5,
                    borderWidth: 0,
                    backgroundColor: "#fff",
                    floating: true,
                    shadow: true,
                    padding: 3,
                    margin: 15,
                    itemDistance: 15
                  },
                  plotOptions:
                  {
                    pie:
                    {
                      allowPointSelect: true,
                      dataLabels:
                      {
                        enabled: true,
                        allowPointSelect: true,
                        cursor: "pointer",
                        //distance: -50,
                        connector: false,
                        connectorColor: "transparent",
                        format: "{point.y} {point.name} "
                      },
                      showInLegend: true,
                      /*startAngle: -90,
                      endAngle: 90,
                      center: ["50%", "75%"]*/
                    }
                  },
                  chart:
                  {
                    plotBackgroundColor: null,
                    plotBorderWidth: 0,
                    plotShadow: false,
                    marginTop: 15
                  },
                  tooltip: {
                       pointFormat: "<b>{point.y}</b> candidates ({point.percentage:.1f}%)"
                  },
                  title:
                  {
                    text: "Created or set in play<br /> by '.$sUser.'",
                    align: "left",
                    verticalAlign: "top"
                  },
                  series: [{
                    type: "pie",
                    name: "Candidate repartititon",
                    color: "#5485B9",
                    data: ['.$sData.']
                  }]
              });
            });
            </script><br class="floatHack"/>
            <span class="light small"> * Candidates the analyst has created or personaly set in play during the period.<br />See details below.</span>';

      }



      $asGrade = $oSlateVars->getCandidateGradeList();
      $asStatus = $oSlateVars->getCandidateStatusList();

      $anNewCandidate = array();
      $anNewCandidate['meetings']['done'] = 0;
      $anNewCandidate['meetings']['pending'] = 0;
      $anNewCandidate['meetings']['cancelled'] = 0;

      $anNewCandidate['grade']['No grade'] = 0;
      $anNewCandidate['grade']['Met'] = 0;
      $anNewCandidate['grade']['Low notable'] = 0;
      $anNewCandidate['grade']['High notable'] = 0;
      $anNewCandidate['grade']['Top shelf'] = 0;

      $anNewCandidate['status']['Name collect'] = 0;
      $anNewCandidate['status']['Contacted'] = 0;
      $anNewCandidate['status']['Pre-screened'] = 0;
      $anNewCandidate['status']['Phone assessed'] = 0;
      $anNewCandidate['status']['Assessed in person'] = 0;
      $anNewCandidate['status']['Placed'] = 0;
      $anNewCandidate['status']['Lost'] = 0;
      $anNewCandidate['status']['Interview set -> contacted'] = 0;


      //--------------------------------------------------------------
      //--------------------------------------------------------------
      // List meetings

      $asMeeting = $this->_getModel()->getMeetings($pasLoginFk, $psDateStart, $psDateEnd);
      $asHead = array('<div class="small">Candidate</div>', '<div class="small">Meeting date</div>',
          '<div class="small">Created by</div>', '<div class="small">Attendee</div>', '<div>Status</div>');

      $sHTML.= $this->_oDisplay->getFloatHack();
      $sHTML.= $this->_oDisplay->getCR();
      $sHTML.= $this->_oDisplay->getBlocStart('', array('style' => 'float: left; margin: 10px 15px 0 5px;'));

      $sHTML.= $this->_oDisplay->getTitle('Meetings ('.count($asMeeting).')', 'h3', true);
      $sHTML.= $this->_oDisplay->getCR();

        $sHTML.= $this->_oDisplay->getBlocStart('', array('class' => 'pipelineList group_stat'));

        $sHTML.= '<div class="stat_row stat_row_header">'.implode('', $asHead).'</div>';
        foreach($asMeeting as $asData)
        {
          if($asData['meeting_done'] == 1)
            $asData['meeting_done'] = 'done';
          elseif($asData['meeting_done'] == 0)
            $asData['meeting_done'] = 'pending';
          else
            $asData['meeting_done'] = 'cancelled';

          $anNewCandidate['meetings'][$asData['meeting_done']]++;

          $sURL = $this->_oPage->getAjaxUrl('555-001', CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, (int)$asData['candidatefk']);
          $asRowData = array(
            '<div class="alignleft small"><a href="javascript:;" onclick="view_candi(\''.$sURL.'\');" >#'.$asData['candidatefk'].' - '.$asData['firstname'].' '.$asData['lastname'].'</a></div>',
            '<div class="alignleft small">'.$asData['date_meeting'].'</div>',
            '<div class="small">'.$oLogin->getUserLink((int)$asData['created_by']).'</div>',
            '<div class="small">'.$oLogin->getUserLink((int)$asData['attendeefk']).'</div>',
            '<div style="text-align: center; color: #CE5A21;">'.$asData['meeting_done'].'</div>');

          $sHTML.= '<div class="stat_row">'.implode('', $asRowData).'</div>';
        }

        $sHTML.= $this->_oDisplay->getBlocEnd();
        $sHTML.= '<div class="floatHack"></div>';

      $sHTML.= $this->_oDisplay->getBlocEnd();



      //--------------------------------------------------------------
      //--------------------------------------------------------------
      // New candidates

      $asCandidates = $this->_getModel()->getNewCandidates($pasLoginFk, $psDateStart, $psDateEnd);
      $asHead = array('<div class="wide">Candidate</div>', '<div class="wide">Created on</div>',
          '<div>Status</div>', '<div>grade</div>');

      $sHTML.= $this->_oDisplay->getFloatHack();
      $sHTML.= $this->_oDisplay->getCR();
      $sHTML.= $this->_oDisplay->getBlocStart('', array('style' => 'float: left; margin: 10px 15px 0 5px;'));

      $sHTML.= $this->_oDisplay->getTitle('New candidates ('.count($asCandidates).')', 'h3', true);
      $sHTML.= $this->_oDisplay->getCR();

        $sHTML.= $this->_oDisplay->getBlocStart('', array('class' => 'pipelineList group_stat'));

        $sHTML.= '<div class="stat_row stat_row_header">'.implode('', $asHead).'</div>';

        foreach($asCandidates as $asData)
        {
          $anNewCandidate['grade'][$asGrade[$asData['grade']]]++;
          $anNewCandidate['status'][$asStatus[$asData['statusfk']]]++;

          $sURL = $this->_oPage->getAjaxUrl('555-001', CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, (int)$asData['sl_candidatepk']);
          $asRowData = array(
            '<div class="alignleft wide"><a href="javascript:;" onclick="view_candi(\''.$sURL.'\');" >#'.$asData['sl_candidatepk'].' - '.$asData['firstname'].' '.$asData['lastname'].'</a></div>',
            '<div class="alignleft wide">'.$asData['date_created'].'</div>',
            '<div>'.$asStatus[$asData['statusfk']].'</div>', '<div>'.$asGrade[$asData['grade']].'</div>');

          $sHTML.= '<div class="stat_row">'.implode('', $asRowData).'</div>';
        }

        $sHTML.= $this->_oDisplay->getBlocEnd();
        $sHTML.= '<div class="floatHack"></div>';

      $sHTML.= $this->_oDisplay->getBlocEnd();




      //--------------------------------------------------------------
      //--------------------------------------------------------------
      // Pipeline


      $asCandidate = $this->_getModel()->getPiplelineCandidate($pasLoginFk, $psDateStart, $psDateEnd, 251);
      $asHead = array('<div class="wide">Candidate</div>', '<div class="wide">Position title</div>', '<div>Status</div>', '<div>Expires on</div>');

      $sHTML.= $this->_oDisplay->getFloatHack();
      $sHTML.= $this->_oDisplay->getCR();
      $sHTML.= $this->_oDisplay->getBlocStart('', array('style' => 'float: left; margin: 10px 15px 0 5px;'));

      $sHTML.= $this->_oDisplay->getTitle('Active candidates in the pipeline ('.count($asCandidate).')', 'h3', true);
      $sHTML.= $this->_oDisplay->getCR();



        $asRow = array();
        foreach($asCandidate as $asData)
        {
          $sURL = $this->_oPage->getAjaxUrl('555-001', CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, (int)$asData['candidatefk']);
          $sPositionURL = $this->_oPage->getAjaxUrl('555-005', CONST_ACTION_VIEW, CONST_POSITION_TYPE_JD, (int)$asData['positionfk']);

          if($nUser == (int)$asData['candi_created'])
          {
            if(!isset($anNewCandidate['play']['created']))
              $anNewCandidate['play']['created'] = 1;
            else
              $anNewCandidate['play']['created']++;
          }
          elseif($nUser == (int)$asData['created_by'])
          {
            if(!isset($anNewCandidate['played']))
              $anNewCandidate['play']['played'] = 1;
            else
              $anNewCandidate['play']['played']++;
          }

          $asRowData = array(
            '<div class="alignleft wide">
              <a href="javascript:;" onclick="view_candi(\''.$sURL.'\');" >#'.$asData['candidatefk'].' - '.$asData['firstname'].' '.$asData['lastname'].'</a>
              <br /><span class="light small">by '.$oLogin->getUserLink((int)$asData['candi_created'], true).'</span></div>',
            '<div class="alignleft wide"><a href="javascript:;" onclick="view_position(\''.$sPositionURL.'\');" >'.'#'.$asData['positionfk'].' - '.$asData['position_title'].'</a></div>',
            '<div style="text-align: center; color: #CE5A21;">'.$this->casStatus[(int)$asData['position_status']].'
              <br /><span class="light small">by '.$oLogin->getUserLink((int)$asData['created_by'], true).'</span></div>',
            '<div>'.date('Y-m-d', strtotime($asData['date_expire'])).'</div>');

          $asRow[] = '<div class="stat_row">'.implode('', $asRowData).'</div>';
        }


        $sHTML.= $this->_oDisplay->getBlocStart('', array('class' => 'pipelineList group_stat small_line'));
        $sHTML.= '<div class="stat_row stat_row_header">'.implode('', $asHead).'</div>';
        $sHTML.= implode('', $asRow);
        $sHTML.= $this->_oDisplay->getBlocEnd();
        $sHTML.= '<div class="floatHack"></div>';


       $sHTML.= $this->_oDisplay->getTitle($sUser.'\'s stats from '.$psDateStart.' to '.$psDateEnd, 'h3', true);
       foreach($anNewCandidate as $sCategory => $asCounter)
       {
          $sHTML.= '<span class="summary_number_title">Candidates '.$sCategory.':</span><br />';

          $asData = array();
          foreach($asCounter as $sType => $nCount)
          {
             $asData[]= '<span class="summary_number">'.$sType.': '.$nCount.'</span>';

          }
          $sHTML.= implode('', $asData).'<br />';
       }


      $sHTML.= $this->_oDisplay->getBlocEnd();


      return $sHTML;
    }

    private function _getGraphPageList()
    {
      $sHTML = '';
      $sHTML.= $this->_oDisplay->getTitle('Chart list');

      $sHTML.= '
        <script>
        $(document).ready(function()
        {
          $(".options_loc a").click(function()
          {
            $(".urls .loc").html("&location="+$(this).text()).css("font-weight", "bold");
          });

          $(".options_period a").click(function()
          {
            $(".urls .period").html("&period="+$(this).text()).css("font-weight", "bold");
          });
        });

        </script>

        <span class="options_loc">
        Change locations: <a>all</a> <a>tokyo</a>, <a>manila</a>, <a>hongkong</a>, <a>singapore</a>, <a>canada</a> <a>grp&grp=116</a><br />
        </span>

        <span class="options_period">
        Change period: <a>month</a>, <a>q1</a>, <a>q2</a>, <a>q3</a>, <a>q4</a>, <a>s1</a>, <a>s2</a>, <a>year</a>, custom ( &date_start=2013-01-01&date_end=2014-10-10 )<br />
        </span>
        <br /><br />

        grp: 1 - 10 (fin, lifescience,auto,it,cns,other,law,admin,hr)<br />
        grp: 103 manager, 105 admin, 106 tokyo, 107 manila, 108 consultant, 109 researcher, 110 HKong, 113 Canada, 114 analyst<br />
        grp: 115 active, 116 real users, 117 singapore <br /><br />



        <div class="urls" style="border: 1px solid #dedede; padding: 10px;">
        <a href="/index.php5?uid=555-006&ppa=ppal&ppt=stat&ppk=0&pg=0&stat_type=kpi&watercooler=1&chart_only=1&period=custom&date_start=2013-01-01&date_end=2014-10-10&location=all&chart=met">KPI - Met candidates</a> :<br />
         /index.php5?uid=555-006&ppa=ppal&ppt=stat&ppk=0&pg=0&stat_type=kpi&watercooler=1&chart_only=1<span class="period">&period=custom&date_start=2013-01-01&date_end=2014-10-10</span><span class="loc">&location=all</span>&chart=met
        <br /><br />

        <a href="/index.php5?uid=555-006&ppa=ppal&ppt=stat&ppk=0&pg=0&stat_type=kpi&watercooler=1&chart_only=1&period=custom&date_start=2013-01-01&date_end=2014-10-10&location=all&chart=set_vs_met">KPI - Set VS Met </a>:<br />
         /index.php5?uid=555-006&ppa=ppal&ppt=stat&ppk=0&pg=0&stat_type=kpi&watercooler=1&chart_only=1<span class="period">&period=custom&date_start=2013-01-01&date_end=2014-10-10</span><span class="loc">&location=all</span>&chart=set_vs_met
        <br /><br />

        <a href="/index.php5?uid=555-006&ppa=ppal&ppt=stat&ppk=0&pg=0&stat_type=kpi&watercooler=1&chart_only=1&period=custom&date_start=2013-01-01&date_end=2014-10-10&location=all&chart=in_play">KPI - In_play</a>: <br />
         /index.php5?uid=555-006&ppa=ppal&ppt=stat&ppk=0&pg=0&stat_type=kpi&watercooler=1&chart_only=1<span class="period">&period=custom&date_start=2013-01-01&date_end=2014-10-10</span><span class="loc">&location=all</span>&chart=in_play
        <br /><br />

        <a href="/index.php5?uid=555-006&ppa=ppal&ppt=stat&ppk=0&pg=0&stat_type=kpi&watercooler=1&chart_only=1&period=custom&date_start=2013-01-01&date_end=2014-10-10&location=all&chart=contrib">KPI - Contributors</a>: <br />
         /index.php5?uid=555-006&ppa=ppal&ppt=stat&ppk=0&pg=0&stat_type=kpi&watercooler=1&chart_only=1<span class="period">&period=custom&date_start=2013-01-01&date_end=2014-10-10</span><span class="loc">&location=all</span>&chart=contrib
        <br /><br />

        <a href="/index.php5?uid=555-006&ppa=pprev&ppt=revenue&ppk=0&watercooler=1">KPI - Revenue</a>: <br />
         /index.php5?uid=555-006&ppa=pprev&ppt=revenue&ppk=0&watercooler=1<span class="loc">&location=all</span>
        <br /><br />

        </div>
        ';

      return $sHTML;
    }



    private function _getContributorChart($psDateStart, $psDateEnd, $pasChartData, $psTitle)
    {
      //dump($pasChartData);
      $pbAjax = false;

      $asChartData = array();
      $nCount = 0;

      foreach($pasChartData as $nLoginPk => $anContrib)
      {
        $sUserName = $this->casAllUserData[$nLoginPk]['pseudo'];

        $asChartData['contrib'][$sUserName] = 0;
        $asChartData['placement'][] = $anContrib['placement'];
        $asChartData['active'][] = $anContrib['active'];
        $nCount++;
      }

      arsort($asChartData['contrib']);
      //dump($asChartData);

      if($this->cbWatercooler)
      {
        $sLabelOption = ',labels: { rotation: -45 } ';
      }
      else
        $sLabelOption = '';


      $sId = uniqid();
      $sHTML = '<br />';

      if($pbAjax)
        $sHTML.= '<div id="sicChart_'.$sId.'" style="height: 250px; width: 780px;  margin: 0 auto;"></div>';
      else
        $sHTML.= '<div id="sicChart_'.$sId.'" style="min-height: 750px; width: 100%;  margin: 0 auto;"></div>';

      $sHTML.= '<script>
        $(function()
        {
          $("#sicChart_'.$sId.'").highcharts(
          {
            chart:
            {
              events :
              {
                load : edgeExtend,
                redraw : edgeExtend
              }
            },
            title: {
                text: ""
            }';

        if($this->cbDisplayLegend)
        {
          $sHTML.= ',
            legend: {
                layout: "vertical",
                align: "right",
                verticalAlign: "middle",
                borderWidth: 0
            }';
        }

        $sHTML.= ',
            plotOptions:
            {
              spline:
              {
                marker: { enabled: true },
                lineWidth: 1,
                states: {hover: {lineWidth: 2 }}
              },
              column:
              {
                stacking: "normal"
              }
            },
            xAxis: {
              categories: ["'.implode('","', array_keys($asChartData['contrib'])).'"]
              '.$sLabelOption.'
            },
            yAxis:
            {
                title:
                {
                  text: "Contibutions to play/placements"
                }
            },
            tooltip: {
              shared: true,
              useHTML: true,
              formatter: function()
              {
                var s = "<div class=\'chartTooltip\'>"+ this.x +"</div>";

                $.each(this.points, function(i, point)
                {
                  s += "<span style=\'color:"+point.series.color+"\'>" + point.series.name +"</span>: " + point.y+"<br/>";
                });
                return s;
              }
            },
            series: [
            {
              type: "column",
              name: "Contibuted to placements",
              data: ['.implode(',', $asChartData['placement']).'],
              color: "#F7B94F",
              dataLabels:
              {
                enabled: true,
                style:
                {
                  color: "#FFFFFF",
                  fontWeight: "bold",
                  fontSize: 20
                },
                align: "center",
                x: 0,
                y: 20,
                formatter: function()
                {
                  if(this.y == 0)
                    return "";

                  return this.y;
                }
              }
             },
             {
              type: "column",
              name: "Contibuted to active / play",
              data: ['.implode(',', $asChartData['active']).'],
              color: "#5485B9",
              dataLabels:
              {
                enabled: true,
                style:
                {
                  color: "#FFFFFF",
                  fontWeight: "bold",
                  fontSize: 20
                },
                align: "center",
                x: 0,
                y: 20,
                formatter: function()
                {
                  if(this.y == 0)
                    return "";

                  return this.y;
                }
              }
             }
           ]
        });
      });
      </script>';

     return $sHTML;
    }

    private function getSelectedRevenueChart()
    {
      //ChromePhp::log('getSelectedRevenueChart');
      // '/index.php5?uid=555-006&ppa=asrc&ppt=revenue&ppk=0&watercooler=1'
      //https://beta.slate.co.jp/index.php5?uid=555-006&amp;ppa=asrc&amp;ppt=revenue&amp;watercooler=1&amp;ppk=0
      //$year = $_GET['year'];
      //$chartName = $_GET['name'];

      //$url = '/index.php5?uid=555-006&ppa=pprev&ppt=revenue&ppk=0&watercooler=1&year='.$next_year;

      $this->get_revenue_chart();
    }

    private function get_revenue_chart($year = '',$chartName = '')
    {
      //ChromePhp::log('get_revenue_chart');
      //echo 'test'; // mca MCA
      //exit;
      //ChromePhp::log('get_revenue_chart');
      $revenueChartLoop = get_revenue_chart_loop();
      //ChromePhp::log($revenueChartLoop);
      $nextloop = 0;
      if(isset($_GET['nextloop']))
      {
        $nextloop = $_GET['nextloop'];
      }

      $loopInformation = $revenueChartLoop[$nextloop];
      $loopInformation = explode('-',$loopInformation);
      $loopYear = $loopInformation[0];
      $loopChart = $loopInformation[1];

      $nextloop++;
      if($nextloop > 4)
      {
        $nextloop = 0;
      }

      $this->cbWatercooler = (bool)getValue('watercooler');
      $location = getValue('location', 'All');
      //$year = $next_year = getValue('year', date('Y'));
      $year = $next_year = (int)$loopYear;

      $swap_time = 1000 * 60; // 1 minute
      $url = '/index.php5?uid=555-006&ppa=pprev&ppt=revenue&ppk=0&watercooler=1&year='.$next_year;

      if(!empty($this->cbWatercooler))
      {
        //add class to hide everything except charts
        $this->_oPage->addCssFile($this->getResourcePath().'/css/watercooler.css');
      }

      if (!is_numeric($year))
      {
        $year = date('Y');
      }

      $isRevenue = true;
      //ChromePhp::log($nextloop);
      if($loopChart == 'totals_chart_ordered')
      {
        $isRevenue = false;
        $html = $this->get_general_total_chart($nextloop);
      }
      elseif($loopChart == 'candidates_met_bar_chart')
      {
        //0000-00-00 00:00:00
        $user_ids = array();
        $new_candidate_met = array();
        $thisYear = date('Y');
        $thisMonth = date('m');
        $start_date = $thisYear.'-'.$thisMonth.'-01 00:00:00';
        $start_date_title = $date('M').' 01 '.$thisYear;
        $end_date = date('Y-m-d H:i:s');

        $title = "Candidates Met ".$start_date_title." to Present";

        $consultants = get_active_consultants();
        $new_candidate_met_json = '';
        $new_candidate_count = '';
        foreach ($consultants as $key => $value)
        {
          $consultant_id = $value['loginpk'];
          $new_candidate_met[$consultant_id]['count'] = get_objectives_new_candidate_met($consultant_id, $start_date, $end_date);
          $new_candidate_met[$consultant_id]['consultant_name'] = substr($value['firstname'],0,1).".".$value['lastname'];
          $new_candidate_met[$consultant_id]['formatted'] = $new_candidate_met[$consultant_id]['consultant_name']." |".$new_candidate_met[$consultant_id]['count']."|";
          //$new_candidate_met_json.= $new_candidate_met[$consultant_id]['formatted'].";";
          //$new_candidate_count .=$new_candidate_met[$consultant_id]['count'].";";
        }

        uasort($new_candidate_met, sort_multi_array_by_value('count', 'reverse'));

        foreach ($new_candidate_met as $key => $value)
        {
          $new_candidate_met_json.= $value['formatted'].";";
          $new_candidate_count .=$value['count'].";";
        }

      }
      elseif($loopChart == 'resume_bar_chart')
      {

        $rs_ccm1_mccm_formatted = "";
        $rs_ccm1_mccm_rsc = "";
        $rs_ccm1_mccm_ccm1 = "";
        $rs_ccm1_mccm_mccm = "";

        $consultants = get_active_consultants();
        $rs_ccm1_mccm = array();

        $thisYear = date('Y');
        $thisMonth = date('m');
        $start_date = $thisYear.'-'.$thisMonth.'-01 00:00:00';
        $start_date_title = $date('M').' 01 '.$thisYear;

        $title = "Resume sent / CCM1 / MCCM ".$start_date_title." to Present";

        foreach ($consultants as $key => $value)
        {
          $consultant_id = $value['loginpk'];
          $resume_sent_temp = get_resume_sent_count($consultant_id, $start_date);
          $rs_ccm1_mccm[$consultant_id]['resume_sent'] = $resume_sent_temp['count'];
          $rs_ccm1_mccm[$consultant_id]['ccm1'] = get_ccm1_count($consultant_id, $start_date);
          //ChromePhp::log($rs_ccm1_mccm[$consultant_id]['ccm1']);
          $rs_ccm1_mccm[$consultant_id]['mccm'] = get_mccm_count($consultant_id, $start_date);
          $rs_ccm1_mccm[$consultant_id]['formatted'] = substr($value['firstname'],0,1).".".$value['lastname']." |".$resume_sent_temp['count']."|";
        }
        uasort($rs_ccm1_mccm, sort_multi_array_by_value('resume_sent', 'reverse'));
        foreach ($rs_ccm1_mccm as $key => $value)
        {
          $rs_ccm1_mccm_formatted.= $value['formatted'].";";
          $rs_ccm1_mccm_rsc.=$value['resume_sent'].";";
          $rs_ccm1_mccm_ccm1.=$value['ccm1']['count'].";";
          $rs_ccm1_mccm_mccm.=$value['mccm']['count'].";";
        }

      }
      elseif($loopChart == 'candidate_in_play_bar_chart')
      {
        $inplay_formatted = "";
        $inplay_count = "";
        $inplay_rsc = "";

        $consultants = get_active_consultants();
        $inplay = array();

        $thisYear = date('Y');
        $thisMonth = date('m');
        $start_date = $thisYear.'-'.$thisMonth.'-01 00:00:00';
        $start_date_title = $date('M').' 01 '.$thisYear;

        $title = "Candidates in play / Resume sent ".$start_date_title." to Present";

        foreach ($consultants as $key => $value)
        {
          $consultant_id = $value['loginpk'];
          $resume_sent_temp = get_resume_sent_count($consultant_id, $start_date);
          $inplay[$consultant_id]['resume_sent'] = $resume_sent_temp['count'];

          $candidate_inplay_temp = get_candidate_in_play($consultant_id, $start_date);
          $inplay[$consultant_id]['candidate_inplay'] = $candidate_inplay_temp['count'];

          $inplay[$consultant_id]['formatted'] = substr($value['firstname'],0,1).".".$value['lastname']." |".$candidate_inplay_temp['count']."|";
        }
        uasort($inplay, sort_multi_array_by_value('candidate_inplay', 'reverse'));
        foreach ($inplay as $key => $value)
        {
          $inplay_formatted.= $value['formatted'].";";
          $inplay_count.=$value['candidate_inplay'].";";
          $inplay_rsc.=$value['resume_sent'].";";
        }
      }
      else
      {
        $revenue_data = $this->_getModel()->get_revenue_data($year);
      }

      $this->_oPage->addCssFile($this->getResourcePath().'/css/revenue.css');

      $data = array('revenue_data' => $revenue_data, 'location' => $location, 'year' => $year, 'row_number_rank' => 1, 'total_paid' => 0,
        'total_signed' => 0, 'total_placed' => 0, 'decimals' => 0, 'display_object' => $this->_oDisplay, 'url' => $url,'swap_time' => $swap_time,'nextloop' => $nextloop
        );

      if(isset($new_candidate_met) && !empty($new_candidate_met))
      {
        $data['new_candidate_met'] = $new_candidate_met;
        $data['new_candidate_met_json'] = $new_candidate_met_json;
        $data['new_candidate_count'] = $new_candidate_count;
        $data['title'] = $title;
      }
      if(isset($rs_ccm1_mccm) && !empty($rs_ccm1_mccm))
      {
        $data['rs_ccm1_mccm_formatted'] = $rs_ccm1_mccm_formatted;
        $data['rs_ccm1_mccm_rsc'] = $rs_ccm1_mccm_rsc;
        $data['rs_ccm1_mccm_ccm1'] = $rs_ccm1_mccm_ccm1;
        $data['rs_ccm1_mccm_mccm'] = $rs_ccm1_mccm_mccm;
        $data['title'] = $title;
      }
      if(isset($inplay) && !empty($inplay))
      {
        $data['inplay_formatted'] = $inplay_formatted;
        $data['inplay_count'] = $inplay_count;
        $data['inplay_rsc'] = $inplay_rsc;
        $data['title'] = $title;
      }

      //$html = $this->_oDisplay->render('revenue_chart', $data);
      if($isRevenue)
      {
        $html = $this->_oDisplay->render($loopChart, $data);
      }

      return $html;
    }

    private function get_ccm_chart()
    {
      $this->cbWatercooler = (bool)getValue('watercooler');
      $location = getValue('location', 'All');
      $start_date = getValue('year', '');
      $end_date = getValue('year', '');

      $swap_time = 1000 * 60 * 2; //2 minute
      $url = '/index.php5?uid=555-006&ppa=pprev&ppt=revenue&ppk=0&watercooler=1';

      if(!empty($this->cbWatercooler))
      {
        //add class to hide everything except charts
        $this->_oPage->addCssFile($this->getResourcePath().'/css/watercooler.css');
      }

      $oChart = CDependency::getComponentByName('charts');
      $oChart->includeChartsJs();

      $this->_oPage->addJsFile(self::getResourcePath().'/js/highchart_extend.js');

      if (!is_numeric($start_date))
        $start_date = date('Y-01-01 00:00:00');

      if (!is_numeric($end_date))
        $end_date = date('Y-12-31 23:59:59');

      $ccm_data = $this->_getModel()->get_ccm_data($start_date, $end_date);

      $ccm_count = $names = array();

      foreach ($ccm_data as $value)
      {
        $ccm_count[] = $value['ccm_count'];
        $names[] = '"'.$value['name'].'"';
      }


      $html = '<div id="title" style="overflow: auto; margin-bottom: 40px;">
        <div class="h3" style="float: left;">
          Total CCM1 &nbsp;|&nbsp; Dates: '.date('Y').'-Jan-01 to '.date('Y').'-Dec-31
        </div>
      </div>';

      $html .= '<div id="ccm_chart" style="height: 600px;;  margin: 0 auto; width: 95%"></div>';


      $html .= '<script>
        $(function () {
          $("#ccm_chart").highcharts({
              chart:
              {
                events :
                {
                  load : edgeExtend,
                  redraw : edgeExtend
                },
                marginBottom: 80
              },
              title:
              {
                  text: ""
              },
              xAxis:
              {
                  categories: ['.implode(',', $names).'],
                  crosshair: true,
                  labels: {
                    style: {
                        fontSize:\'20px\'
                    }
                  }
              },
              yAxis:
              {
                  min: 0,
                  title: {
                      text: ""
                  },
                  labels: {
                    style: {
                        fontSize:\'30px\'
                    }
                  }
              },
              tooltip:
              {
                  headerFormat: "<span style=\'font-size:20px\'>{point.key}</span><table>",
                  pointFormat: "<tr><td style=\'color:{series.color};padding:0;font-size:20px\'>{series.name}: </td>" +
                      "<td style=\'padding:0;;font-size:20px;font-weight:bold\'>{point.y}</td></tr>",
                  footerFormat: "</table>",
                  shared: true,
                  useHTML: true
              },
              plotOptions:
              {
                  bar: {
                      pointPadding: 0.2,
                      borderWidth: 0
                  }
              },
              series:
              [{
                  type: "bar",
                  name: "Total CCM1",
                  pointWidth: 25,
                  data: ['.implode(',', $ccm_count).'],
                  dataLabels:
                  {
                    enabled: true,
                    /*rotation: -90,*/
                    style:
                    {
                      color: "#FFFFFF",
                      fontWeight: "bold"
                    },
                    align: "center",
                    x: -10,
                    y: 0
                  }
              }]
          });
      });

      // setTimeout(function(){ window.location.replace("'.$url.'"); }, ('.$swap_time.'));
      </script>
      ';

      return $html;
    }

    private function get_general_total_chart($nextloop = '666')
    {
      $start_date = $start_date_original = getValue('start_date', '');
      $end_date = $end_date_original = getValue('end_date', '');

      if(empty($start_date))
      {
        $start_date = date('Y-m').'-01 00:00:00';
        $start_date_original = date('Y-m').'-01';
        $viewStart = $start_date;
      }
      else
        $start_date .= ' 00:00:00';

      if(empty($end_date))
      {
        $end_date = date('Y-m-t').' 23:59:59';
        $end_date_original = date('Y-m-t');
        $viewEnd = $end_date;
      }
      else
        $end_date .= ' 23:59:59';

      $dateNow = date('Y-m-d');
      $generatedKPIs = getGeneratedKpi($dateNow);
      $generatedKPIsCount = count($generatedKPIs);
      $data = array();
      $submit_totals = getValue('submit_totals');

      if($nextloop != '666')
      {
        $generatedKPIsCount = 0;
      }

      if($submit_totals == 'Get totals' || $generatedKPIsCount == 0)
      {
        $all_ids = $promoted_ids = $promote_dates = $consultant_names = $consultant_ids = $researcher_names = $researcher_ids = array();
        $stats_data = array();
        $consultant_skip_id = array(389, 315, 354, 186);
        $researcher_skip_id = array(301, 423, 475, 315, 474, 487, 486, 259);

        // generate consultant data
        foreach ($this->casUserByGroup[108] as $key => $value)
        {
          if ($value['status'])
          {
            //echo "<br><br>";
            //var_dump($value);
            $consultant_names[$key] = substr($value['firstname'], 0, 1).'. '.$value['lastname'];
            if($value['r_to_c_date'] == "0000-00-00 00:00:00")
            {
              $consultant_ids[] = $key;
            }
            else
            {
              //echo "<br><br>".$start_date." - ".$value['r_to_c_date'];
              if($start_date < $value['r_to_c_date'] && $end_date > $value['r_to_c_date'])
              {
                //echo "<br><br>INSIDE";
                $promoted_ids[] = $key;
                $consultant_ids[] = $key;

                $start_date_researcher = $start_date;
                $end_date_researcher = $value['r_to_c_date'];

                $start_date_consultant = $value['r_to_c_date'];
                $end_date_consultant = $end_date;
              }
              else
              {
                $consultant_ids[] = $key;
              }

            }
            $promote_dates[$key] = $value['r_to_c_date'];
            $all_ids[] = $key;
          }
        }

        $temp_set_vs_met = $this->_getModel()->getKpiSetVsMet($consultant_ids, $start_date, $end_date, 'consultant');
        $temp_resume_sent = $this->_getModel()->get_resume_sent($consultant_ids, $start_date, $end_date, 'consultant');
        $temp_ccm = $this->_getModel()->get_ccm_data($consultant_ids, $start_date, $end_date, 'consultant');
        $temp_in_play = $this->_getModel()->get_new_in_play($consultant_ids, $start_date, $end_date, 'consultant');
        $temp_placement = $this->_getModel()->get_placement_number($consultant_ids, $start_date, $end_date, 'consultant');
        $temp_offer = $this->_getModel()->get_offer_sent($consultant_ids, $start_date, $end_date, 'consultant');
        $temp_new_candidate_met = $this->_getModel()->get_new_candidate_met($consultant_ids, $start_date , $end_date, 'consultant');

        //echo "<br><br>";
        //var_dump($temp_new_candidate_met);
        if(isset($promoted_ids[0]))
        {
          //echo "<br><br>ICEDER consultant";
          //var_dump($promoted_ids);
          $temp_set_vs_met_promote = $this->_getModel()->getKpiSetVsMet($promoted_ids, $start_date_consultant, $end_date_consultant, 'consultant');
          $temp_resume_sent_promote = $this->_getModel()->get_resume_sent($promoted_ids, $start_date_consultant, $end_date_consultant, 'consultant');
          $temp_ccm_promote = $this->_getModel()->get_ccm_data($promoted_ids, $start_date_consultant, $end_date_consultant, 'consultant');
          $temp_in_play_promote = $this->_getModel()->get_new_in_play($promoted_ids, $start_date_consultant, $end_date_consultant, 'consultant');
          $temp_placement_promote = $this->_getModel()->get_placement_number($promoted_ids, $start_date_consultant, $end_date_consultant, 'consultant');
          $temp_offer_promote = $this->_getModel()->get_offer_sent($promoted_ids, $start_date_consultant, $end_date_consultant, 'consultant');
          $temp_new_candidate_met_promote = $this->_getModel()->get_new_candidate_met($promoted_ids, $start_date_consultant , $end_date_consultant, 'consultant');

          //echo "<br><br>";
          //var_dump($promoted_ids);

          //-------------promoted-------------
          //-------------promoted-------------
          foreach ($promoted_ids as $id)
          {
            if (in_array($id, $consultant_skip_id))
              continue;

            $user_info = getUserInformaiton($id);

            $stats_data['consultant'][$id]['promoteFlag'] = "true";

            if (!empty($temp_resume_sent_promote[$id]['resumes_sent']))
            {
              $stats_data['consultant'][$id]['resumes_sent'] = $temp_resume_sent_promote[$id]['resumes_sent'];
              $stats_data['consultant'][$id]['resumes_sent_info'] = $temp_resume_sent_promote[$id]['resumes_sent_info'];
            }
            else
            {
              $stats_data['consultant'][$id]['resumes_sent'] = 0;
              $stats_data['consultant'][$id]['resumes_sent_info'] = array();
            }

            if (!empty($temp_set_vs_met_promote[$id]['set']))
            {
              $stats_data['consultant'][$id]['set'] = $temp_set_vs_met_promote[$id]['set'];
              $stats_data['consultant'][$id]['set_meeting_info'] = $temp_set_vs_met_promote[$id]['set_meeting_info'];
            }
            else
            {
              $stats_data['consultant'][$id]['set'] = 0;
              $stats_data['consultant'][$id]['set_meeting_info'] = array();
            }

            if (!empty($temp_set_vs_met_promote[$id]['met']))
            {
              $stats_data['consultant'][$id]['met'] = $temp_set_vs_met_promote[$id]['met'];
              $stats_data['consultant'][$id]['met_meeting_info'] = $temp_set_vs_met_promote[$id]['met_meeting_info'];

            }
            else
            {
              $stats_data['consultant'][$id]['met'] = 0;
              $stats_data['consultant'][$id]['met_meeting_info'] = array();
            }

              $stats_data['consultant'][$id]['ccm1'] = $temp_ccm_promote[$id]['ccm1'];
              $stats_data['consultant'][$id]['ccm1_done'] = $temp_ccm_promote[$id]['ccm1_done'];
              $stats_data['consultant'][$id]['ccm1_info'] = $temp_ccm_promote[$id]['ccm_info']['ccm1'];

              $stats_data['consultant'][$id]['ccm2'] = $temp_ccm_promote[$id]['ccm2'];
              $stats_data['consultant'][$id]['ccm2_done'] = $temp_ccm_promote[$id]['ccm2_done'];
              $stats_data['consultant'][$id]['ccm2_info'] = $temp_ccm_promote[$id]['ccm_info']['ccm2'];

              $stats_data['consultant'][$id]['mccm'] = $temp_ccm_promote[$id]['mccm'];
              $stats_data['consultant'][$id]['mccm_done'] = $temp_ccm_promote[$id]['mccm_done'];
              $stats_data['consultant'][$id]['mccm_info'] = $temp_ccm_promote[$id]['ccm_info']['mccm'];


            if (!empty($temp_in_play_promote[$id]['new_candidates']))
            {
              $count = count($temp_in_play_promote[$id]['new_candidates']);
              $stats_data['consultant'][$id]['new_candidates'] = $count;
              $stats_data['consultant'][$id]['new_candidate_info'] = $temp_in_play_promote[$id]['new_candidates'];
            }
            else
            {
              $stats_data['consultant'][$id]['new_candidates'] = 0;
              $stats_data['consultant'][$id]['new_candidate_info'] = array();
            }

            if (!empty($temp_in_play_promote[$id]['new_positions']))
            {
              $count = count($temp_in_play_promote[$id]['new_positions']);
              $stats_data['consultant'][$id]['new_positions'] = $count;
              $stats_data['consultant'][$id]['new_position_info'] = $temp_in_play_promote[$id]['new_positions'];
            }
            else
            {
              $stats_data['consultant'][$id]['new_positions'] = 0;
              $stats_data['consultant'][$id]['new_position_info'] = array();
            }

            if (!empty($temp_placement_promote[$id]['placed']))
            {
              $stats_data['consultant'][$id]['placed'] = $temp_placement_promote[$id]['placed'];
              $stats_data['consultant'][$id]['placed_info'] = $temp_placement_promote[$id]['placed_info'];

            }
            else
            {
              $stats_data['consultant'][$id]['placed'] = 0;
              $stats_data['consultant'][$id]['placed_info'] = array();
            }

            if (!empty($temp_offer_promote[$id]['offers_sent']))
            {
              $stats_data['consultant'][$id]['offers_sent'] = $temp_offer_promote[$id]['offers_sent'];
              $stats_data['consultant'][$id]['offer_info'] = $temp_offer_promote[$id]['offer_info'];
            }
            else
            {
              $stats_data['consultant'][$id]['offers_sent'] = 0;
              $stats_data['consultant'][$id]['offer_info'] = array();
            }

            if (!empty($temp_new_candidate_met_promote[$id]))
            {
              $count = count($temp_new_candidate_met_promote[$id]);

              $stats_data['consultant'][$id]['new_candidate_met_count'] = $count;
              $stats_data['consultant'][$id]['new_candidate_met_info'] = $temp_new_candidate_met_promote[$id];
            }
            else
            {
              $stats_data['consultant'][$id]['new_candidate_met_count'] = 0;
              $stats_data['consultant'][$id]['new_candidate_met_info'] = array();
            }
            $stats_data['consultant'][$id]['name'] = $consultant_names[$id];
            if($promote_dates[$id] == "0000-00-00 00:00:00")
            {
              $stats_data['consultant'][$id]['promote_date'] = "0";
            }
            else
            {
              $stats_data['consultant'][$id]['promote_date'] = $promote_dates[$id];
            }
            $stats_data['consultant'][$id]['user_id'] = $id;

          }
        }
        //-------------promoted-------------
        //-------------promoted-------------

        foreach ($consultant_ids as $id)
        {
          if (in_array($id, $consultant_skip_id))
            continue;

          $user_info = getUserInformaiton($id);
          $stats_data['consultant'][$id]['kpi_flag'] = $user_info['kpi_flag'];

          if($user_info['r_to_c_date'] != "0000-00-00 00:00:00")
          {
            if($start_date < $user_info['r_to_c_date'] && $end_date < $user_info['r_to_c_date'])
            {
              $stats_data['consultant'][$id]['position'] = "Researcher";
            }
            else
            {
              $stats_data['consultant'][$id]['position'] = $user_info['position'];
            }
          }
          else
          {
            $stats_data['consultant'][$id]['position'] = $user_info['position'];
          }

          if (!empty($temp_resume_sent[$id]['resumes_sent']))
          {
            $stats_data['consultant'][$id]['resumes_sent'] = $temp_resume_sent[$id]['resumes_sent'];
            $stats_data['consultant'][$id]['resumes_sent_info'] = $temp_resume_sent[$id]['resumes_sent_info'];
          }
          else
          {
            $stats_data['consultant'][$id]['resumes_sent'] = 0;
            $stats_data['consultant'][$id]['resumes_sent_info'] = array();
          }

          if (!empty($temp_set_vs_met[$id]['set']))
          {
            $stats_data['consultant'][$id]['set'] = $temp_set_vs_met[$id]['set'];
            $stats_data['consultant'][$id]['set_meeting_info'] = $temp_set_vs_met[$id]['set_meeting_info'];
          }
          else
          {
            $stats_data['consultant'][$id]['set'] = 0;
            $stats_data['consultant'][$id]['set_meeting_info'] = array();
          }

          if (!empty($temp_set_vs_met[$id]['met']))
          {
            $stats_data['consultant'][$id]['met'] = $temp_set_vs_met[$id]['met'];
            $stats_data['consultant'][$id]['met_meeting_info'] = $temp_set_vs_met[$id]['met_meeting_info'];
            //echo '<br><br><br>';
            //var_dump($stats_data['consultant'][$id]['met_meeting_info']);
            //echo '<br><br><br>';
          }
          else
          {
            $stats_data['consultant'][$id]['met'] = 0;
            $stats_data['consultant'][$id]['met_meeting_info'] = array();
          }

          //if (!empty($temp_ccm[$id]['ccm1']) || !empty($temp_ccm[$id]['ccm1_done']))
          //{
            $stats_data['consultant'][$id]['ccm1'] = $temp_ccm[$id]['ccm1'];
            $stats_data['consultant'][$id]['ccm1_done'] = $temp_ccm[$id]['ccm1_done'];
            $stats_data['consultant'][$id]['ccm1_info'] = $temp_ccm[$id]['ccm_info']['ccm1'];
          //}
          /*else
          {
            $stats_data['consultant'][$id]['ccm1'] = 0;
            $stats_data['consultant'][$id]['ccm1_done'] = 0;
            $stats_data['consultant'][$id]['ccm1_info'] = array();
          }*/

          //if (!empty($temp_ccm[$id]['ccm2']))
          //{
            $stats_data['consultant'][$id]['ccm2'] = $temp_ccm[$id]['ccm2'];
            $stats_data['consultant'][$id]['ccm2_done'] = $temp_ccm[$id]['ccm2_done'];
            $stats_data['consultant'][$id]['ccm2_info'] = $temp_ccm[$id]['ccm_info']['ccm2'];
          //}
          /*else
          {
            $stats_data['consultant'][$id]['ccm2'] = 0;
            $stats_data['consultant'][$id]['ccm2_done'] = 0;
            $stats_data['consultant'][$id]['ccm2_info'] = array();
          }*/

          //if (!empty($temp_ccm[$id]['mccm']))
          //{
            $stats_data['consultant'][$id]['mccm'] = $temp_ccm[$id]['mccm'];
            $stats_data['consultant'][$id]['mccm_done'] = $temp_ccm[$id]['mccm_done'];
            $stats_data['consultant'][$id]['mccm_info'] = $temp_ccm[$id]['ccm_info']['mccm'];
          //}
          /*else
          {
            $stats_data['consultant'][$id]['mccm'] = 0;
            $stats_data['consultant'][$id]['mccm_done'] = 0;
            $stats_data['consultant'][$id]['mccm_info'] = array();
          }*/

  //----------------------------------------------------------------------------------
          if (!empty($temp_in_play[$id]['new_candidates']))
          {
            $count = count($temp_in_play[$id]['new_candidates']);
            $stats_data['consultant'][$id]['new_candidates'] = $count;
            $stats_data['consultant'][$id]['new_candidate_info'] = $temp_in_play[$id]['new_candidates'];
          }
          else
          {
            $stats_data['consultant'][$id]['new_candidates'] = 0;
            $stats_data['consultant'][$id]['new_candidate_info'] = array();
          }

          if (!empty($temp_in_play[$id]['new_positions']))
          {
            $count = count($temp_in_play[$id]['new_positions']);
            $stats_data['consultant'][$id]['new_positions'] = $count;
            $stats_data['consultant'][$id]['new_position_info'] = $temp_in_play[$id]['new_positions'];
          }
          else
          {
            $stats_data['consultant'][$id]['new_positions'] = 0;
            $stats_data['consultant'][$id]['new_position_info'] = array();
          }
  //----------------------------------------------------------------------------------

          if (!empty($temp_placement[$id]['placed']))
          {
            $stats_data['consultant'][$id]['placed'] = $temp_placement[$id]['placed'];
            $stats_data['consultant'][$id]['placed_info'] = $temp_placement[$id]['placed_info'];

          }
          else
          {
            $stats_data['consultant'][$id]['placed'] = 0;
            $stats_data['consultant'][$id]['placed_info'] = array();
          }

          if (!empty($temp_offer[$id]['offers_sent']))
          {
            $stats_data['consultant'][$id]['offers_sent'] = $temp_offer[$id]['offers_sent'];
            $stats_data['consultant'][$id]['offer_info'] = $temp_offer[$id]['offer_info'];
          }
          else
          {
            $stats_data['consultant'][$id]['offers_sent'] = 0;
            $stats_data['consultant'][$id]['offer_info'] = array();
          }

          if (!empty($temp_new_candidate_met[$id]))
          {
            $count = count($temp_new_candidate_met[$id]);

            $stats_data['consultant'][$id]['new_candidate_met_count'] = $count;
            $stats_data['consultant'][$id]['new_candidate_met_info'] = $temp_new_candidate_met[$id];
          }
          else
          {
            $stats_data['consultant'][$id]['new_candidate_met_count'] = 0;
            $stats_data['consultant'][$id]['new_candidate_met_info'] = array();
          }
          $stats_data['consultant'][$id]['name'] = $consultant_names[$id];
          if($promote_dates[$id] == "0000-00-00 00:00:00")
          {
            $stats_data['consultant'][$id]['promote_date'] = "0";
          }
          else
          {
            $stats_data['consultant'][$id]['promote_date'] = $promote_dates[$id];
          }
          $stats_data['consultant'][$id]['user_id'] = $id;

        }

        $temp_set_vs_met = $temp_resume_sent = $temp_ccm = array();

        // generate researcher data
        foreach ($this->casUserByGroup[109] as $key => $value)
        {
          if ($value['status'])
          {
            $researcher_names[$key] = substr($value['firstname'], 0, 1).'. '.$value['lastname'];
            $researcher_ids[] = $key;
          }
        }
  //echo "<br><br>";
  //var_dump($stats_data['consultant']['457']);
        $temp_set_vs_met = $this->_getModel()->getKpiSetVsMet($researcher_ids, $start_date, $end_date);
        $temp_resume_sent = $this->_getModel()->get_resume_sent($researcher_ids, $start_date, $end_date);
        $temp_ccm = $this->_getModel()->get_ccm_data($researcher_ids, $start_date, $end_date);
        $temp_in_play = $this->_getModel()->get_new_in_play($researcher_ids, $start_date, $end_date);
        $temp_placement = $this->_getModel()->get_placement_number($researcher_ids, $start_date, $end_date);
        $temp_offer = $this->_getModel()->get_offer_sent($researcher_ids, $start_date, $end_date);
        $temp_new_candidate_met = $this->_getModel()->get_new_candidate_met($researcher_ids, $start_date , $end_date);

        //---------------promoted
        //---------------promoted
        if(isset($promoted_ids[0]))
        {
          //echo "<br><br>iceride researcher";
          $temp_set_vs_met_promoted = $this->_getModel()->getKpiSetVsMet($promoted_ids, $start_date_researcher, $end_date_researcher);
          $temp_resume_sent_promoted = $this->_getModel()->get_resume_sent($promoted_ids, $start_date_researcher, $end_date_researcher);
          $temp_ccm_promoted = $this->_getModel()->get_ccm_data($promoted_ids, $start_date_researcher, $end_date_researcher);
          $temp_in_play_promoted = $this->_getModel()->get_new_in_play($promoted_ids, $start_date_researcher, $end_date_researcher);
          $temp_placement_promoted = $this->_getModel()->get_placement_number($promoted_ids, $start_date_researcher, $end_date_researcher);
          $temp_offer_promoted = $this->_getModel()->get_offer_sent($promoted_ids, $start_date_researcher, $end_date_researcher);
          $temp_new_candidate_met_promoted = $this->_getModel()->get_new_candidate_met($promoted_ids, $start_date_researcher , $end_date_researcher);

          foreach ($promoted_ids as $id)
          {
            if (in_array($id, $researcher_skip_id))
              continue;

            $user_info = getUserInformaiton($id);

            $stats_data['researcher'][$id]['promoteFlag'] = "true";


            if (!empty($temp_resume_sent_promoted[$id]['resumes_sent']))
            {
              $stats_data['researcher'][$id]['resumes_sent'] = $temp_resume_sent_promoted[$id]['resumes_sent'];
              $stats_data['researcher'][$id]['resumes_sent_info'] = $temp_resume_sent_promoted[$id]['resumes_sent_info'];
            }
            else
            {
              $stats_data['researcher'][$id]['resumes_sent'] = 0;
              $stats_data['researcher'][$id]['resumes_sent_info'] = array();
            }

            if (!empty($temp_set_vs_met_promoted[$id]['set']))
            {
              $stats_data['researcher'][$id]['set'] = $temp_set_vs_met_promoted[$id]['set'];
              $stats_data['researcher'][$id]['set_meeting_info'] = $temp_set_vs_met_promoted[$id]['set_meeting_info'];


            }
            else
            {
              $stats_data['researcher'][$id]['set'] = 0;
              $stats_data['researcher'][$id]['set_meeting_info'] = array();
            }

              $count = count($temp_new_candidate_met_promoted[$id]);

              $stats_data['researcher'][$id]['met'] = $count;
              $stats_data['researcher'][$id]['met_meeting_info'] = $temp_new_candidate_met_promoted[$id];

              $stats_data['researcher'][$id]['ccm1'] = $temp_ccm_promoted[$id]['ccm1'];
              $stats_data['researcher'][$id]['ccm1_done'] = $temp_ccm_promoted[$id]['ccm1_done'];
              $stats_data['researcher'][$id]['ccm1_info'] = $temp_ccm_promoted[$id]['ccm_info']['ccm1'];

              $stats_data['researcher'][$id]['ccm2'] = $temp_ccm_promoted[$id]['ccm2'];
              $stats_data['researcher'][$id]['ccm2_done'] = $temp_ccm_promoted[$id]['ccm2_done'];
              $stats_data['researcher'][$id]['ccm2_info'] = $temp_ccm_promoted[$id]['ccm_info']['ccm2'];

              $stats_data['researcher'][$id]['mccm'] = $temp_ccm_promoted[$id]['mccm'];
              $stats_data['researcher'][$id]['mccm_done'] = $temp_ccm_promoted[$id]['mccm_done'];
              $stats_data['researcher'][$id]['mccm_info'] = $temp_ccm_promoted[$id]['ccm_info']['mccm'];


            if (!empty($temp_ccm_promoted[$id]['placedRevenue']))
            {
              $stats_data['researcher'][$id]['placedRevenue'] = $temp_ccm_promoted[$id]['placedRevenue']; $id."_".$temp_ccm_promoted[$id]['placedRevenue'];
              $array = $temp_ccm_promoted[$id]['placedRevenue_info'];

              $finalArray = array();
              foreach ($array['placedRevenue'] as $key => $first) {
                $addArray['candidate'] = $first['candidate'];
                array_push($finalArray,$addArray);
              }

              $stats_data['researcher'][$id]['placedRevenue_info'] = $finalArray;

            }
            else
            {
              $stats_data['researcher'][$id]['placedRevenue'] = 0;
              $stats_data['researcher'][$id]['placedRevenue_info'] = array();
            }
            //------------------------------------------------------------
            if (!empty($temp_in_play_promoted[$id]['new_candidates']))
            {
              $count = count($temp_in_play_promoted[$id]['new_candidates']);
              $stats_data['researcher'][$id]['new_candidates'] = $count;
              $stats_data['researcher'][$id]['new_candidate_info'] = $temp_in_play_promoted[$id]['new_candidates'];
            }
            else
            {
              $stats_data['researcher'][$id]['new_candidates'] = 0;
              $stats_data['researcher'][$id]['new_candidate_info'] = array();
            }

            if (!empty($temp_in_play_promoted[$id]['new_positions']))
            {
              $count = count($temp_in_play_promoted[$id]['new_positions']);
              $stats_data['researcher'][$id]['new_positions'] = $count;
              $stats_data['researcher'][$id]['new_position_info'] = $temp_in_play_promoted[$id]['new_positions'];
            }
            else
            {
              $stats_data['researcher'][$id]['new_positions'] = 0;
              $stats_data['researcher'][$id]['new_position_info'] = array();
            }
            //----------------------------------------------------------

            if (!empty($temp_placement_promoted[$id]['placed']))
            {
              $stats_data['researcher'][$id]['placed'] = $temp_placement_promoted[$id]['placed'];
              $stats_data['researcher'][$id]['placed_info'] = $temp_placement_promoted[$id]['placed_info'];
            }
            else
            {
              $stats_data['researcher'][$id]['placed'] = 0;
              $stats_data['researcher'][$id]['placed_info'] = array();
            }

            if (!empty($temp_offer_promoted[$id]['offers_sent']))
            {
              $stats_data['researcher'][$id]['offers_sent'] = $temp_offer_promoted[$id]['offers_sent'];
              $stats_data['researcher'][$id]['offer_info'] = $temp_offer_promoted[$id]['offer_info'];
            }
            else
            {
              $stats_data['researcher'][$id]['offers_sent'] = 0;
              $stats_data['researcher'][$id]['offer_info'] = array();
            }

            if (!empty($temp_new_candidate_met_promoted[$id]))
            {
              $count = count($temp_new_candidate_met_promoted[$id]);

              $stats_data['researcher'][$id]['new_candidate_met_count'] = $count;
              $stats_data['researcher'][$id]['new_candidate_met_info'] = $temp_new_candidate_met_promoted[$id];
            }
            else
            {
              $stats_data['researcher'][$id]['new_candidate_met_count'] = 0;
              $stats_data['researcher'][$id]['new_candidate_met_info'] = array();
            }

            $stats_data['researcher'][$id]['name'] = $researcher_names[$id];
            $stats_data['researcher'][$id]['promote_date'] = "0";
            $stats_data['researcher'][$id]['user_id'] = $id;
          }

        }
        //---------------promoted
        //---------------promoted

  //echo '<br><br><br>';
  //var_dump($temp_set_vs_met);
  //echo '<br><br><br>';
        foreach ($researcher_ids as $id)
        {
          if (in_array($id, $researcher_skip_id))
            continue;

          $user_info = getUserInformaiton($id);
          $stats_data['researcher'][$id]['kpi_flag'] = $user_info['kpi_flag'];

          if($user_info['r_to_c_date'] != "0000-00-00 00:00:00")
          {
            if($start_date <= $user_info['r_to_c_date'] && $end_date <= $user_info['r_to_c_date'])
            {
              $stats_data['researcher'][$id]['position'] = "Researcher";
            }
            else
            {
              $stats_data['researcher'][$id]['position'] = $user_info['position'];
            }
          }
          else
          {
            $stats_data['researcher'][$id]['position'] = $user_info['position'];
          }

          //$stats_data['researcher'][$id]['position'] = $user_info['position'];

          if (!empty($temp_resume_sent[$id]['resumes_sent']))
          {
            $stats_data['researcher'][$id]['resumes_sent'] = $temp_resume_sent[$id]['resumes_sent'];
            $stats_data['researcher'][$id]['resumes_sent_info'] = $temp_resume_sent[$id]['resumes_sent_info'];
          }
          else
          {
            $stats_data['researcher'][$id]['resumes_sent'] = 0;
            $stats_data['researcher'][$id]['resumes_sent_info'] = array();
          }

          if (!empty($temp_set_vs_met[$id]['set']))
          {
            $stats_data['researcher'][$id]['set'] = $temp_set_vs_met[$id]['set'];
            $stats_data['researcher'][$id]['set_meeting_info'] = $temp_set_vs_met[$id]['set_meeting_info'];

          }
          else
          {
            $stats_data['researcher'][$id]['set'] = 0;
            $stats_data['researcher'][$id]['set_meeting_info'] = array();
          }

          //if (!empty($temp_set_vs_met[$id]['met']))
          //{
            //$stats_data['researcher'][$id]['met'] = $temp_set_vs_met[$id]['met'];
            //$stats_data['researcher'][$id]['met_meeting_info'] = $temp_set_vs_met[$id]['met_meeting_info'];

  // NOTE: researcher larda met ile new candidate met ayni olsun istendi !!!!!!
            $count = count($temp_new_candidate_met[$id]);

            $stats_data['researcher'][$id]['met'] = $count;
            $stats_data['researcher'][$id]['met_meeting_info'] = $temp_new_candidate_met[$id];

            //var_dump($stats_data['researcher'][$id]['met_meeting_info']);
          //}
          /*else
          {
            $stats_data['researcher'][$id]['met'] = 0;
             $stats_data['researcher'][$id]['met_meeting_info'] = array();
          }*/

          if (!empty($temp_ccm[$id]['ccm1']))
          {
            $stats_data['researcher'][$id]['ccm1'] = $temp_ccm[$id]['ccm1'];
            $stats_data['researcher'][$id]['ccm1_done'] = $temp_ccm[$id]['ccm1_done'];
            $stats_data['researcher'][$id]['ccm1_info'] = $temp_ccm[$id]['ccm_info']['ccm1'];
          }
          else
          {
            $stats_data['researcher'][$id]['ccm1'] = 0;
            $stats_data['researcher'][$id]['ccm1_done'] = 0;
            $stats_data['researcher'][$id]['ccm1_info'] = array();
          }

          if (!empty($temp_ccm[$id]['ccm2']))
          {
            $stats_data['researcher'][$id]['ccm2'] = $temp_ccm[$id]['ccm2'];
            $stats_data['researcher'][$id]['ccm2_done'] = $temp_ccm[$id]['ccm2_done'];
            $stats_data['researcher'][$id]['ccm2_info'] = $temp_ccm[$id]['ccm_info']['ccm2'];
          }
          else
          {
            $stats_data['researcher'][$id]['ccm2'] = 0;
            $stats_data['researcher'][$id]['ccm2_done'] = 0;
            $stats_data['researcher'][$id]['ccm2_info'] = array();
          }

          if (!empty($temp_ccm[$id]['mccm']))
          {
            $stats_data['researcher'][$id]['mccm'] = $temp_ccm[$id]['mccm'];
            $stats_data['researcher'][$id]['mccm_done'] = $temp_ccm[$id]['mccm_done'];
            $stats_data['researcher'][$id]['mccm_info'] = $temp_ccm[$id]['ccm_info']['mccm'];
          }
          else
          {
            $stats_data['researcher'][$id]['mccm'] = 0;
            $stats_data['researcher'][$id]['mccm_done'] = 0;
            $stats_data['researcher'][$id]['mccm_info'] = array();
          }

          if (!empty($temp_ccm[$id]['placedRevenue']))
          {
            $stats_data['researcher'][$id]['placedRevenue'] = $temp_ccm[$id]['placedRevenue']; $id."_".$temp_ccm[$id]['placedRevenue'];
            $array = $temp_ccm[$id]['placedRevenue_info'];

            $finalArray = array();
            foreach ($array['placedRevenue'] as $key => $first) {
              $addArray['candidate'] = $first['candidate'];
              array_push($finalArray,$addArray);
            }

            $stats_data['researcher'][$id]['placedRevenue_info'] = $finalArray;
            //var_dump($stats_data['researcher'][$id]['placedRevenue_info']);
            //exit;

  /*echo $id.'<br><br><br>';
            foreach ($temp_ccm[$id]['placedRevenue_info'] as $key1 => $first) {
              foreach ($first as $key => $second) {
                //var_dump($second);echo'<br>';
                $ekle['candidate'] = $second['candidate'];
                array_push($array,$ekle);
                echo'<ekle - >';var_dump($ekle);echo'<br>';
                //array_push($stats_data['researcher'][$id]['placedRevenue_info'],$ekle);
              }
            }
            $stats_data['researcher'][$id]['placedRevenue_info'] = $array;
            var_dump($stats_data['researcher'][$id]['placedRevenue']);
            echo "<br><br>";
            var_dump($stats_data['researcher'][$id]['placedRevenue_info']);
            exit;*/
          }
          else
          {
            $stats_data['researcher'][$id]['placedRevenue'] = 0;
            $stats_data['researcher'][$id]['placedRevenue_info'] = array();
          }
          //------------------------------------------------------------
          if (!empty($temp_in_play[$id]['new_candidates']))
          {
            $count = count($temp_in_play[$id]['new_candidates']);
            $stats_data['researcher'][$id]['new_candidates'] = $count;
            $stats_data['researcher'][$id]['new_candidate_info'] = $temp_in_play[$id]['new_candidates'];
          }
          else
          {
            $stats_data['researcher'][$id]['new_candidates'] = 0;
            $stats_data['researcher'][$id]['new_candidate_info'] = array();
          }

          if (!empty($temp_in_play[$id]['new_positions']))
          {
            $count = count($temp_in_play[$id]['new_positions']);
            $stats_data['researcher'][$id]['new_positions'] = $count;
            $stats_data['researcher'][$id]['new_position_info'] = $temp_in_play[$id]['new_positions'];
          }
          else
          {
            $stats_data['researcher'][$id]['new_positions'] = 0;
            $stats_data['researcher'][$id]['new_position_info'] = array();
          }
          //----------------------------------------------------------

          if (!empty($temp_placement[$id]['placed']))
          {
            $stats_data['researcher'][$id]['placed'] = $temp_placement[$id]['placed'];
            $stats_data['researcher'][$id]['placed_info'] = $temp_placement[$id]['placed_info'];
          }
          else
          {
            $stats_data['researcher'][$id]['placed'] = 0;
            $stats_data['researcher'][$id]['placed_info'] = array();
          }

          if (!empty($temp_offer[$id]['offers_sent']))
          {
            $stats_data['researcher'][$id]['offers_sent'] = $temp_offer[$id]['offers_sent'];
            $stats_data['researcher'][$id]['offer_info'] = $temp_offer[$id]['offer_info'];
          }
          else
          {
            $stats_data['researcher'][$id]['offers_sent'] = 0;
            $stats_data['researcher'][$id]['offer_info'] = array();
          }

          if (!empty($temp_new_candidate_met[$id]))
          {
            $count = count($temp_new_candidate_met[$id]);

            $stats_data['researcher'][$id]['new_candidate_met_count'] = $count;
            $stats_data['researcher'][$id]['new_candidate_met_info'] = $temp_new_candidate_met[$id];
          }
          else
          {
            $stats_data['researcher'][$id]['new_candidate_met_count'] = 0;
            $stats_data['researcher'][$id]['new_candidate_met_info'] = array();
          }

          $stats_data['researcher'][$id]['name'] = $researcher_names[$id];
          $stats_data['researcher'][$id]['promote_date'] = "0";
          $stats_data['researcher'][$id]['user_id'] = $id;
        }

        uasort($stats_data['researcher'], sort_multi_array_by_value('name'));
        uasort($stats_data['consultant'], sort_multi_array_by_value('name'));

        $allCanidatesArray = array();
        // -- CONSULTANT CANDIDATE LINKS IN ORDER--
        // -- CONSULTANT CANDIDATE LINKS IN ORDER--
        // -- CONSULTANT CANDIDATE LINKS IN ORDER--
        foreach ($consultant_ids as $key => $id)
        {
          foreach ($stats_data['consultant'][$id]['set_meeting_info'] as $key => $candidate)
          {
            if(isset($candidate['candidate']))
            {
              $candidate_id = $candidate['candidate'];
              $candidateInfo = getCandidateInformation($candidate_id);
              $candidateNotes = getCandidateNotes($candidate_id);

              $allCanidatesArray['consultant'][$id][$candidate_id]['setFlag'] = '1';
              $allCanidatesArray['consultant'][$id][$candidate_id]['hoverTooltip'] = $candidateInfo['firstname']." ".$candidateInfo['lastname']." (".$candidateInfo['age'].")<br>".$candidateInfo['title']."<br>".$candidateInfo['company_name']."<br><br><i>".$candidateNotes['content']."</i>";
            }
          }

          foreach ($stats_data['consultant'][$id]['met_meeting_info'] as $key => $candidate)
          {
            if(isset($candidate['candidate']))
            {
              $candidate_id = $candidate['candidate'];
              $candidateInfo = getCandidateInformation($candidate_id);
              $candidateNotes = getCandidateNotes($candidate_id);
            }
            else if(isset($candidate['candidatefk']))
            {
              $candidate_id = $candidate['candidatefk'];
              $candidateInfo = getCandidateInformation($candidate_id);
              $candidateNotes = getCandidateNotes($candidate_id);
            }
            if(isset($candidate_id))
            {
              $allCanidatesArray['consultant'][$id][$candidate_id]['metFlag'] = '1';
              $allCanidatesArray['consultant'][$id][$candidate_id]['hoverTooltip'] = $candidateInfo['firstname']." ".$candidateInfo['lastname']." (".$candidateInfo['age'].")<br>".$candidateInfo['title']."<br>".$candidateInfo['company_name']."<br><br><i>".$candidateNotes['content']."</i>";
            }
          }

          foreach ($stats_data['consultant'][$id]['resumes_sent_info'] as $key => $candidate)
          {
            if(isset($candidate['candidate']))
            {
              $candidate_id = $candidate['candidate'];
              $candidateInfo = getCandidateInformation($candidate_id);
              $candidateNotes = getCandidateNotes($candidate_id);
              $allCanidatesArray['consultant'][$id][$candidate_id]['resumeSentFlag'] = '1';
              $allCanidatesArray['consultant'][$id][$candidate_id]['hoverTooltip'] = $candidateInfo['firstname']." ".$candidateInfo['lastname']." (".$candidateInfo['age'].")<br>".$candidateInfo['title']."<br>".$candidateInfo['company_name']."<br><br><i>".$candidateNotes['content']."</i>";

            }
          }

          foreach ($stats_data['consultant'][$id]['ccm1_info'] as $key => $candidate)
          {
            if(isset($candidate['candidate']))
            {
              $candidate_id = $candidate['candidate']; // CCM1 SET
              $candidateInfo = getCandidateInformation($candidate_id);
              $candidateNotes = getCandidateNotes($candidate_id);
              $allCanidatesArray['consultant'][$id][$candidate_id]['ccm1SetFlag'] = '1';
              $allCanidatesArray['consultant'][$id][$candidate_id]['hoverTooltip'] = $candidateInfo['firstname']." ".$candidateInfo['lastname']." (".$candidateInfo['age'].")<br>".$candidateInfo['title']."<br>".$candidateInfo['company_name']."<br><br><i>".$candidateNotes['content']."</i>";
            }

          }

          foreach ($stats_data['consultant'][$id]['ccm1_info'] as $key => $candidate)
          {
            if(isset($candidate['ccm_done_candidate']))
            {
              $candidate_id = $candidate['ccm_done_candidate']; // CCM1 DONE

              $allCanidatesArray['consultant'][$id][$candidate_id]['ccm1DoneFlag'] = '1';
            }
          }

          foreach ($stats_data['consultant'][$id]['ccm2_info'] as $key => $candidate)
          {
            if(isset($candidate['candidate']))
            {
              $candidate_id = $candidate['candidate']; // CCM2 SET
              $candidateInfo = getCandidateInformation($candidate_id);
              $candidateNotes = getCandidateNotes($candidate_id);
              $allCanidatesArray['consultant'][$id][$candidate_id]['ccm2SetFlag'] = '1';
              $allCanidatesArray['consultant'][$id][$candidate_id]['hoverTooltip'] = $candidateInfo['firstname']." ".$candidateInfo['lastname']." (".$candidateInfo['age'].")<br>".$candidateInfo['title']."<br>".$candidateInfo['company_name']."<br><br><i>".$candidateNotes['content']."</i>";
            }

          }

          foreach ($stats_data['consultant'][$id]['ccm2_info'] as $key => $candidate)
          {
            if(isset($candidate['ccm_done_candidate']))
            {
              $candidate_id = $candidate['ccm_done_candidate']; // CCM2 DONE

              $allCanidatesArray['consultant'][$id][$candidate_id]['ccm2DoneFlag'] = '1';
            }

          }

          foreach ($stats_data['consultant'][$id]['mccm_info'] as $key => $candidate)
          {
            if(isset($candidate['candidate']))
            {
              $candidate_id = $candidate['candidate']; // MCCM SET
              $candidateInfo = getCandidateInformation($candidate_id);
              $candidateNotes = getCandidateNotes($candidate_id);
              $allCanidatesArray['consultant'][$id][$candidate_id]['mccmSetFlag'] = '1';
              $allCanidatesArray['consultant'][$id][$candidate_id]['hoverTooltip'] = $candidateInfo['firstname']." ".$candidateInfo['lastname']." (".$candidateInfo['age'].")<br>".$candidateInfo['title']."<br>".$candidateInfo['company_name']."<br><br><i>".$candidateNotes['content']."</i>";
            }

          }

          foreach ($stats_data['consultant'][$id]['mccm_info'] as $key => $candidate)
          {

            if(isset($candidate['ccm_done_candidate']))
            {

              $candidate_ids = $candidate['ccm_done_candidate']; // MCCM done

              foreach ($candidate_ids as $key => $candidate_id)
              {
                $allCanidatesArray['consultant'][$id][$candidate_id]['mccmDoneFlag'] = '1';
              }
            }
          }

          foreach ($stats_data['consultant'][$id]['new_candidate_met_info'] as $key => $candidate)
          {
            if(isset($candidate['candidatefk']))
            {
              $candidate_id = $candidate['candidatefk']; // new candi met

              $allCanidatesArray['consultant'][$id][$candidate_id]['newCandiMetFlag'] = '1';
            }
          }

          foreach ($stats_data['consultant'][$id]['new_candidate_info'] as $key => $candidate)
          {
            if(isset($candidate['candidatefk']))
            {
              $candidate_id = $candidate['candidatefk']; // new candidate in play

              $allCanidatesArray['consultant'][$id][$candidate_id]['newCandiPlayFlag'] = '1';
            }
          }

          foreach ($stats_data['consultant'][$id]['new_position_info'] as $key => $candidate)
          {
            //var_dump($candidate);
            //echo "<br><br>";
            if(isset($candidate['positionfk']))
            {
              $candidate_id = $candidate['candidatefk']; // New position

              $allCanidatesArray['consultant'][$id][$candidate_id]['newPositionPlayFlag'] = $candidate['positionfk'];
            }
          }

          foreach ($stats_data['consultant'][$id]['offer_info'] as $key => $candidate)
          {
            if(isset($candidate['candidate']))
            {
              $candidate_id = $candidate['candidate']; // Offer
              $candidateInfo = getCandidateInformation($candidate_id);
              $candidateNotes = getCandidateNotes($candidate_id);
              $allCanidatesArray['consultant'][$id][$candidate_id]['offerFlag'] = '1';
              $allCanidatesArray['consultant'][$id][$candidate_id]['hoverTooltip'] = $candidateInfo['firstname']." ".$candidateInfo['lastname']." (".$candidateInfo['age'].")<br>".$candidateInfo['title']."<br>".$candidateInfo['company_name']."<br><br><i>".$candidateNotes['content']."</i>";
            }
          }

          foreach ($stats_data['consultant'][$id]['placed_info'] as $key => $candidate)
          {
            if(isset($candidate['candidate']))
            {
              $candidate_id = $candidate['candidate']; // Placed
              $candidateInfo = getCandidateInformation($candidate_id);
              $candidateNotes = getCandidateNotes($candidate_id);
              $allCanidatesArray['consultant'][$id][$candidate_id]['placedFlag'] = '1';
              $allCanidatesArray['consultant'][$id][$candidate_id]['hoverTooltip'] = $candidateInfo['firstname']." ".$candidateInfo['lastname']." (".$candidateInfo['age'].")<br>".$candidateInfo['title']."<br>".$candidateInfo['company_name']."<br><br><i>".$candidateNotes['content']."</i>";
            }
          }

        }
        // -- CONSULTANT CANDIDATE LINKS IN ORDER--
        // -- CONSULTANT CANDIDATE LINKS IN ORDER--
        // -- CONSULTANT CANDIDATE LINKS IN ORDER--








        // -- RESEARCHER CANDIDATE LINKS IN ORDER--
        // -- RESEARCHER CANDIDATE LINKS IN ORDER--
        // -- RESEARCHER CANDIDATE LINKS IN ORDER--
        foreach ($researcher_ids as $key => $id)
        {
          foreach ($stats_data['researcher'][$id]['set_meeting_info'] as $key => $candidate)
          {
            if(isset($candidate['candidate']))
            {
              $candidate_id = $candidate['candidate'];
              $candidateInfo = getCandidateInformation($candidate_id);
              $candidateNotes = getCandidateNotes($candidate_id);
              $allCanidatesArray['researcher'][$id][$candidate_id]['setFlag'] = '1';
              $allCanidatesArray['researcher'][$id][$candidate_id]['hoverTooltip'] = $candidateInfo['firstname']." ".$candidateInfo['lastname']." (".$candidateInfo['age'].")<br>".$candidateInfo['title']."<br>".$candidateInfo['company_name']."<br><br><i>".$candidateNotes['content']."</i>";
            }
          }

          foreach ($stats_data['researcher'][$id]['met_meeting_info'] as $key => $candidate)
          {
            if(isset($candidate['candidate']))
            {
              $candidate_id = $candidate['candidate'];
              $candidateInfo = getCandidateInformation($candidate_id);
              $candidateNotes = getCandidateNotes($candidate_id);
            }
            else if(isset($candidate['candidatefk']))
            {
              $candidate_id = $candidate['candidatefk'];
              $candidateInfo = getCandidateInformation($candidate_id);
              $candidateNotes = getCandidateNotes($candidate_id);
            }
            if(isset($candidate_id))
            {
              $allCanidatesArray['researcher'][$id][$candidate_id]['metFlag'] = '1';
              $allCanidatesArray['researcher'][$id][$candidate_id]['hoverTooltip'] = $candidateInfo['firstname']." ".$candidateInfo['lastname']." (".$candidateInfo['age'].")<br>".$candidateInfo['title']."<br>".$candidateInfo['company_name']."<br><br><i>".$candidateNotes['content']."</i>";
            }
          }

          foreach ($stats_data['researcher'][$id]['resumes_sent_info'] as $key => $candidate)
          {
            if(isset($candidate['candidate']))
            {
              $candidate_id = $candidate['candidate'];
              $candidateInfo = getCandidateInformation($candidate_id);
              $candidateNotes = getCandidateNotes($candidate_id);
              $allCanidatesArray['researcher'][$id][$candidate_id]['resumeSentFlag'] = '1';
              $allCanidatesArray['researcher'][$id][$candidate_id]['hoverTooltip'] = $candidateInfo['firstname']." ".$candidateInfo['lastname']." (".$candidateInfo['age'].")<br>".$candidateInfo['title']."<br>".$candidateInfo['company_name']."<br><br><i>".$candidateNotes['content']."</i>";

            }
          }

          foreach ($stats_data['researcher'][$id]['ccm1_info'] as $key => $candidate)
          {
            if(isset($candidate['candidate']))
            {
              $candidate_id = $candidate['candidate']; // CCM1 SET
              $candidateInfo = getCandidateInformation($candidate_id);
              $candidateNotes = getCandidateNotes($candidate_id);
              $allCanidatesArray['researcher'][$id][$candidate_id]['ccm1SetFlag'] = '1';
              $allCanidatesArray['researcher'][$id][$candidate_id]['hoverTooltip'] = $candidateInfo['firstname']." ".$candidateInfo['lastname']." (".$candidateInfo['age'].")<br>".$candidateInfo['title']."<br>".$candidateInfo['company_name']."<br><br><i>".$candidateNotes['content']."</i>";
            }

          }

          foreach ($stats_data['researcher'][$id]['ccm1_info'] as $key => $candidate)
          {
            if(isset($candidate['ccm_done_candidate']))
            {
              $candidate_id = $candidate['ccm_done_candidate']; // CCM1 DONE

              $allCanidatesArray['researcher'][$id][$candidate_id]['ccm1DoneFlag'] = '1';
            }
          }

          foreach ($stats_data['researcher'][$id]['ccm2_info'] as $key => $candidate)
          {
            if(isset($candidate['candidate']))
            {
              $candidate_id = $candidate['candidate']; // CCM2 SET
              $candidateInfo = getCandidateInformation($candidate_id);
              $candidateNotes = getCandidateNotes($candidate_id);
              $allCanidatesArray['researcher'][$id][$candidate_id]['ccm2SetFlag'] = '1';
              $allCanidatesArray['researcher'][$id][$candidate_id]['hoverTooltip'] = $candidateInfo['firstname']." ".$candidateInfo['lastname']." (".$candidateInfo['age'].")<br>".$candidateInfo['title']."<br>".$candidateInfo['company_name']."<br><br><i>".$candidateNotes['content']."</i>";
            }

          }

          foreach ($stats_data['researcher'][$id]['ccm2_info'] as $key => $candidate)
          {
            if(isset($candidate['ccm_done_candidate']))
            {
              $candidate_id = $candidate['ccm_done_candidate']; // CCM2 DONE

              $allCanidatesArray['researcher'][$id][$candidate_id]['ccm2DoneFlag'] = '1';
            }

          }

          foreach ($stats_data['researcher'][$id]['mccm_info'] as $key => $candidate)
          {
            if(isset($candidate['candidate']))
            {
              $candidate_id = $candidate['candidate']; // MCCM SET
              $candidateInfo = getCandidateInformation($candidate_id);
              $candidateNotes = getCandidateNotes($candidate_id);
              $allCanidatesArray['researcher'][$id][$candidate_id]['mccmSetFlag'] = '1';
              $allCanidatesArray['researcher'][$id][$candidate_id]['hoverTooltip'] = $candidateInfo['firstname']." ".$candidateInfo['lastname']." (".$candidateInfo['age'].")<br>".$candidateInfo['title']."<br>".$candidateInfo['company_name']."<br><br><i>".$candidateNotes['content']."</i>";
            }

          }

          foreach ($stats_data['researcher'][$id]['mccm_info'] as $key => $candidate)
          {
            if(isset($candidate['ccm_done_candidate']))
            {
              $candidate_id = $candidate['ccm_done_candidate']; // MCCM done

              $allCanidatesArray['researcher'][$id][$candidate_id]['mccmDoneFlag'] = '1';
            }
          }

          foreach ($stats_data['researcher'][$id]['new_candidate_met_info'] as $key => $candidate)
          {
            if(isset($candidate['candidatefk']))
            {
              $candidate_id = $candidate['candidatefk']; // new candi met

              $allCanidatesArray['researcher'][$id][$candidate_id]['newCandiMetFlag'] = '1';
            }
          }

          foreach ($stats_data['researcher'][$id]['new_candidate_info'] as $key => $candidate)
          {
            if(isset($candidate['candidatefk']))
            {
              $candidate_id = $candidate['candidatefk']; // new candidate in play

              $allCanidatesArray['researcher'][$id][$candidate_id]['newCandiPlayFlag'] = '1';
            }
          }

          foreach ($stats_data['researcher'][$id]['new_position_info'] as $key => $candidate)
          {
            //var_dump($candidate);
            //echo "<br><br>";
            if(isset($candidate['positionfk']))
            {
              $candidate_id = $candidate['candidatefk']; // New position

              $allCanidatesArray['researcher'][$id][$candidate_id]['newPositionPlayFlag'] = $candidate['positionfk'];
            }
          }

          foreach ($stats_data['researcher'][$id]['offer_info'] as $key => $candidate)
          {
            if(isset($candidate['candidate']))
            {
              $candidate_id = $candidate['candidate']; // Offer
              $candidateInfo = getCandidateInformation($candidate_id);
              $candidateNotes = getCandidateNotes($candidate_id);
              $allCanidatesArray['researcher'][$id][$candidate_id]['offerFlag'] = '1';
              $allCanidatesArray['researcher'][$id][$candidate_id]['hoverTooltip'] = $candidateInfo['firstname']." ".$candidateInfo['lastname']." (".$candidateInfo['age'].")<br>".$candidateInfo['title']."<br>".$candidateInfo['company_name']."<br><br><i>".$candidateNotes['content']."</i>";
            }
          }

          foreach ($stats_data['researcher'][$id]['placed_info'] as $key => $candidate)
          {
            if(isset($candidate['candidate']))
            {
              $candidate_id = $candidate['candidate']; // Placed
              $candidateInfo = getCandidateInformation($candidate_id);
              $candidateNotes = getCandidateNotes($candidate_id);
              $allCanidatesArray['researcher'][$id][$candidate_id]['placedFlag'] = '1';
              $allCanidatesArray['researcher'][$id][$candidate_id]['hoverTooltip'] = $candidateInfo['firstname']." ".$candidateInfo['lastname']." (".$candidateInfo['age'].")<br>".$candidateInfo['title']."<br>".$candidateInfo['company_name']."<br><br><i>".$candidateNotes['content']."</i>";
            }
          }

        }
        // -- RESEARCHER CANDIDATE LINKS IN ORDER--
        // -- RESEARCHER CANDIDATE LINKS IN ORDER--
        // -- RESEARCHER CANDIDATE LINKS IN ORDER--


  /*echo "<br><br>";
  //var_dump($allCanidatesArray);


        foreach ($allCanidatesArray['researcher']['457'] as $key => $value)
        {
          var_dump($key);
          var_dump($value);
          echo '<br><br>';
        }*/




        $this->_oPage->addJsFile(CONST_PATH_JS_JQUERYUI);
        $this->_oPage->addCSSFile(CONST_PATH_CSS_JQUERYUI);

        $this->_oPage->addCssFile($this->getResourcePath().'/css/totals_chart.css');

        if($generatedKPIsCount == 0)//GET REPORT WITH CLICK
        {
          $viewStart = $start_date;
          $viewEnd = $end_date;
          $dateNow = date('Y_m_d');

          $viewStart = substr($viewStart, 0, -9);
          $viewEnd = substr($viewEnd, 0, -9);

          $viewStart = str_replace('-','_',$viewStart);
          $viewEnd = str_replace('-','_',$viewEnd);

          $jsonData = json_encode($stats_data);

          $oLogin = CDependency::getCpLogin();
          $created_by_id = $oLogin->getUserPk();
          $added = insertGeneratedKpi($jsonData,$created_by_id,'kpi');

          $jsonDataCandi = json_encode($allCanidatesArray);
          $added = insertGeneratedKpi($jsonDataCandi,$created_by_id,'candi');
        }
      }
      else
      {
        $json_from_db = $generatedKPIs[0]['json_data'];
        $stats_data = json_decode($json_from_db, true);

        $candidate_from_db = $generatedKPIs[1]['json_data'];
        $allCanidatesArray = json_decode($candidate_from_db, true);

        $this->_oPage->addJsFile(CONST_PATH_JS_JQUERYUI);
        $this->_oPage->addCSSFile(CONST_PATH_CSS_JQUERYUI);

        $this->_oPage->addCssFile($this->getResourcePath().'/css/totals_chart.css');
      }

      $data = array('stats_data' => $stats_data, 'start_date_original' => $start_date_original,
        'end_date_original' => $end_date_original, 'start_date' => $start_date,'page_obj' => $this->_oPage);
//ChromePhp::log($nextloop);

      $data['nextloop'] = $nextloop;


      $data['allCanidatesArray'] = $allCanidatesArray;
      header_remove('Set-Cookie');

      $html = $this->_oDisplay->render('totals_chart_ordered', $data);

      return $html;
    }

    private function get_call_log_chart()
    {
      $this->cbWatercooler = (bool)getValue('watercooler');
      $period = getValue('period', 'last_week');

      $ignore_users = array(389, 315, 354, 186, 301, 423, 475, 315, 474, 487, 486, 259, 300, 309, 343, 199, 468);

      $previous_week = strtotime('-1 week +1 day');
      $start_week = strtotime('last monday', $previous_week);
      $end_week = strtotime("next sunday", $start_week);

      switch ($period) {
        case 'custom':
          $start_date = getValue('start', '');
          $end_date = getValue('end', '');

          if (!empty($start_date))
            $start_date = date('Y-m-d', strtotime($start_date));
          else
            $start_date = date('Y-m-d', $start_week);

          if (!empty($end_date))
            $end_date = date('Y-m-d', strtotime($end_date));
          else
            $end_date = date('Y-m-d', $end_week);

          $title = $start_date.' - '.$end_date.' - All Calls custom date';
          break;

        case 'previous_month':
          $start_date = date('Y-m', strtotime('previous month')).'-01';
          $end_date = date('Y-m-t', strtotime('previous month'));

          $title = date('F Y', strtotime('previous month')).' - All Calls previous month';
          break;

        case 'this_month':
          $start_date = date('Y-m', $previous_week).'-01';
          $end_date = date('Y-m-t', $previous_week);

          $title = date('F Y', $previous_week).' - All Calls this month';
          break;

        case 'last_week':
        default:
          $start_date = date('Y-m-d', $start_week);
          $end_date = date('Y-m-d', $end_week);

          $title = date('F jS', $start_week).' - '.date('F jS', $end_week).' - All Calls';
          break;
      }

      /*$swap_time = 1000 * 60; // 1 minute
      $url = '/index.php5?uid=555-006&ppa=pprev&ppt=revenue&ppk=0&watercooler=1&year='.$next_year;*/

      if(!empty($this->cbWatercooler))
      {
        //add class to hide everything except charts
        $this->_oPage->addCssFile($this->getResourcePath().'/css/watercooler.css');
      }

      $call_log_data = $this->_getModel()->get_call_log_data($ignore_users, $start_date, $end_date);

      $this->_oPage->addCssFile($this->getResourcePath().'/css/revenue.css');

      $data = array('call_log_data' => $call_log_data, 'row_number_rank' => 1, 'total_paid' => 0,
        'total_signed' => 0, 'total_placed' => 0, 'decimals' => 0, 'display_object' => $this->_oDisplay,
        'ignore_users' => $ignore_users, 'title' => $title
        );

      $html = $this->_oDisplay->render('call_log_chart', $data);

      return $html;
    }

    private function _getClassFromValue($pnValue, $pnTarget)
    {
      if($pnValue >= (0.85 * $pnTarget))
        return 'obj-good';

      if($pnValue < (0.70 * $pnTarget))
        return 'obj-bad';

      return 'obj-average';
    }
}