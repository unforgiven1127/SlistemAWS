<?php

require_once('component/sl_candidate/sl_candidate.class.php5');
require_once('component/sl_candidate/sl_candidate.model.php5');
require_once('component/sl_candidate/sl_candidate.model.ex.php5');
require_once('component/sl_candidate/resources/class/slate_vars.class.php5');
require_once('common/lib/querybuilder.class.php5');


class CSl_candidateEx extends CSl_candidate
{
  private $_oPage = null;
  private $_oDisplay = null;
  private $casUserData = array();
  private $casActiveUser = array();
  private $coSlateVars = null;
  private $casCandidateData = array();

  private $casSettings = array();
  private $csTabSettings = '';
  private $csTplSettings = '';

  private $csSearchId = '';
  private $passResume = '';


  public function __construct()
  {
    $this->_oLogin = CDependency::getCpLogin();

    if($this->_oLogin->isLogged())
    {
      $this->_oPage = CDependency::getCpPage();
      $this->_oDisplay = CDependency::getCpHtml();

      $this->casUserData = $this->_oLogin->getUserData();

      //fetch all candidate display settings
      $oSettings = CDependency::getComponentByName('settings');

      $this->casSettings = $oSettings->getSettings(array('candidate_tabs', 'candidate_template', 'candi_list_field', 'candi_salary_format'), false);
      $this->csTabSettings = $this->casSettings['candidate_tabs'];
      $this->csTplSettings = $this->casSettings['candidate_template'];

      if(empty($this->casSettings['candi_list_field']))
        $this->casSettings['candi_list_field'] = array();
    }

    return true;
  }

  public function getDefaultType()
  {
    return CONST_CANDIDATE_TYPE_CANDI;
  }

  public function getDefaultAction()
  {
    return CONST_ACTION_LIST;
  }
  //====================================================================
  //  accessors
  //====================================================================

  /**
   * specific for SlateVars
   * @return object CSl_candidateModelEx
  */
  public function getModel()
  {
    return $this->_getModel();
  }

  public function getVars()
  {
    if($this->coSlateVars !== null)
      return $this->coSlateVars;

    if(empty($_SESSION['slate_vars']))
    {
      $this->coSlateVars = new CSlateVars();
      $_SESSION['slate_vars'] = serialize($this->coSlateVars);
    }
    else
    {
      $this->coSlateVars = unserialize($_SESSION['slate_vars']);
      if($this->coSlateVars == false)
        assert('false; // could not restore the var object');
    }

    return $this->coSlateVars;
  }

  //====================================================================
  //  interface
  //====================================================================

  /**   !!! Generic component method but linked to interfaces !!!
   *
   * Return an array listing the public "items" the component filtered by the interface
   * @param string $psInterface
   * @return array
   */
  public function getComponentPublicItems($psInterface = '')
  {
    $asItem = array();
    switch($psInterface)
    {
      case 'notification_item':
      case 'searchable':

        $sURL = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_SEARCH, CONST_CANDIDATE_TYPE_CANDI, 0, array('autocomplete' => 1));
        $asItem[] = array(CONST_CP_UID => $this->csUid, CONST_CP_ACTION => CONST_ACTION_VIEW,
            CONST_CP_TYPE => CONST_CANDIDATE_TYPE_CANDI, 'label' => 'Candidate', 'search_url' => $sURL);

        $sURL = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_SEARCH, CONST_CANDIDATE_TYPE_COMP);
        $asItem[] = array(CONST_CP_UID => $this->csUid, CONST_CP_ACTION => CONST_ACTION_VIEW,
            CONST_CP_TYPE => CONST_CANDIDATE_TYPE_COMP, 'label' => 'Company', 'search_url' => $sURL);
        break;

      default:
        $sURL = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_SEARCH, CONST_CANDIDATE_TYPE_CANDI, 0, array('autocomplete' => 1));
        $asItem[] = array(CONST_CP_UID => $this->csUid, CONST_CP_ACTION => CONST_ACTION_VIEW,
            CONST_CP_TYPE => CONST_CANDIDATE_TYPE_CANDI, 'label' => 'Candidate', 'search_url' => $sURL);

        $sURL = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_SEARCH, CONST_CANDIDATE_TYPE_COMP);
        $asItem[] = array(CONST_CP_UID => $this->csUid, CONST_CP_ACTION => CONST_ACTION_VIEW,
            CONST_CP_TYPE => CONST_CANDIDATE_TYPE_COMP, 'label' => 'Company', 'search_url' => $sURL);

        $sURL = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_MEETING);
        $asItem[] = array(CONST_CP_UID => $this->csUid, CONST_CP_ACTION => CONST_ACTION_VIEW,
            CONST_CP_TYPE => CONST_CANDIDATE_TYPE_MEETING, 'label' => 'Candidate meeting ', 'search_url' => $sURL);
    }

    return $asItem;
  }


  //remove if the interface is not used
  public function getPageActions($psAction = '', $psType = '', $pnPk = 0)
  {
    $asActions = array();
    return $asActions;
  }

  /**
   *remove if the interface is not used
   * @return string json encoded
   */
  public function getAjax()
  {
    $this->_processUrl();
    $oPage = CDependency::getCpPage();


    // --------------------------------------------------------------
    //Complex search need 1 entry point on search for both data types

    if($this->csAction == CONST_ACTION_LIST || $this->csAction == CONST_ACTION_SEARCH)
    {
      if(!getValue('searchId') || getValue('clear_search'))
      {
        $this->csSearchId = manageSearchHistory($this->csUid, $this->csType, true);
      }
      else
      {
        /*dump('load search from id: '.getValue('searchId'));
        dump($_POST);*/
        $this->csSearchId = manageSearchHistory($this->csUid, $this->csType);
        //dump($_POST);

        //If no specific sorting value, reload previous sorting values
        if(!getValue('sortfield'))
        {
          $asOrder = getSearchHistory($this->csSearchId, $this->csUid, $this->csType);
          $_POST['sortfield'] = $asOrder['sortfield'];
          $_POST['sortorder'] = $asOrder['sortorder'];
        }
      }
    }



    switch($this->csType)
    {
      case CONST_CANDIDATE_TYPE_CANDI:

        switch($this->csAction)
        {
          case CONST_ACTION_VIEW:
            return json_encode($oPage->getAjaxExtraContent(array('data' => convertToUtf8($this->_getCandidateView($this->cnPk)))));
            break;

          case CANDI_LOG:
            return json_encode($oPage->getAjaxExtraContent(array('data' => convertToUtf8($this->logAjax($this->cnPk)))));
            break;

          case CONTACT_SEEN_MAIL:
            return json_encode($oPage->getAjaxExtraContent(array('data' => convertToUtf8($this->mailAjax($this->cnPk)))));
            break;

          case COMPANY_DUPLI_CONTROL:
            return json_encode($oPage->getAjaxExtraContent(array('data' => convertToUtf8($this->controlCompanyDuplicate($this->cnPk)))));
            break;

          case CONST_ACTION_LIST:
            return json_encode($oPage->getAjaxExtraContent(array('data' => convertToUtf8($this->_getCandidateList(true)))));
            break;

          case CONST_ACTION_ADD:
            return json_encode($oPage->getAjaxExtraContent(array('data' => $this->_getCandidateAddForm($this->cnPk))));
            break;

          case MERGE_COMPANY:
            return json_encode($oPage->getAjaxExtraContent(array('data' => $this->_deleteCompany(), 'UTF-8')));
            //return $this->_deleteCompany();
            break;

          case DELETE_SELECTED_COMPANY:
            return json_encode($oPage->getAjaxExtraContent(array('data' => $this->deleteSelectedCompany(), 'UTF-8')));
            //return $this->_deleteCompany();
            break;

          case CONST_ACTION_SAVEADD:
            return json_encode($oPage->getAjaxExtraContent($this->_saveCandidate($this->cnPk)));
            break;

          case CONST_ACTION_EDIT:
            return json_encode($oPage->getAjaxExtraContent(array('data' => $this->_getCandidateAddForm($this->cnPk), 'UTF-8')));
            break;

          case CONST_ACTION_LOG:
            $this->_accessRmContactDetails($this->cnPk);
            return json_encode(array('data' => 'ok'));
            break;
          case CONST_ACTION_SEARCH:

            if(getValue('autocomplete'))
            {
              return $this->_autocompleteCandidate();
            }

            if(getValue('complex_search'))
            {
              $this->csType = getValue('data_type');
              $oSearch = CDependency::getComponentByName('search');
              $oQB = $oSearch->buildComplexSearchQuery();

              $asError = $oSearch->getError();
              if(!empty($asError))
                return json_encode(array('alert' => implode('<br />', $asError)));
            }
            else
            {
              $oQB = $this->_getModel()->getQueryBuilder();

              require_once('component/sl_candidate/resources/search/quick_search.class.php5');
              $oQS = new CQuickSearch($oQB);
              $sError = $oQS->buildQuickSearch();
              if(!empty($sError))
                return json_encode(array('alert' => $sError));
            }

            $candidateList = $this->_getCandidateList(true, $oQB);

            $return = $oPage->getAjaxExtraContent(array('data' => convertToUtf8($candidateList), 'action' => 'goPopup.removeActive(\'layer\'); initHeaderManager(); '));
            return json_encode($return);
            break;
            case CONST_ACTION_MANAGE:
              $asDuplicate = $this->_getMergeForm($this->cnPk);

              if(isset($asDuplicate['error']))
                return json_encode($oPage->getAjaxExtraContent(array('data' => $asDuplicate['error'])));

              return json_encode($oPage->getAjaxExtraContent(array('data' => convertToUtf8($asDuplicate['data']))));
              break;

              case CONST_ACTION_TRANSFER:

                return json_encode($oPage->getAjaxExtraContent($this->_mergeDeleteCandidate($this->cnPk)));
                break;

        }
        break;

      case CONST_CANDIDATE_TYPE_COMP:

        switch($this->csAction)
        {
          case CONST_ACTION_VIEW:
            return json_encode($oPage->getAjaxExtraContent(array('data' => convertToUtf8($this->_getCompanyView($this->cnPk)))));
            break;

          case CONST_ACTION_ADD:
          case CONST_ACTION_EDIT:
            return json_encode($oPage->getAjaxExtraContent(array('data' => $this->_getCompanyForm($this->cnPk))));
            break;

          case CONST_ACTION_SAVEADD:
          case CONST_ACTION_SAVEEDIT:
            return json_encode($this->_saveCompany($this->cnPk));
            break;

          case CONST_ACTION_LIST:
            //list and search
            /*$asHTML = $this->_getCompanyList();
            $asHTML['data'] = convertToUtf8($asHTML['data']);
            return json_encode($oPage->getAjaxExtraContent($asHTML));*/
            $oQB = $this->_getModel()->getQueryBuilder();

            require_once('component/sl_candidate/resources/search/quick_search.class.php5');
            $oQS = new CQuickSearch($oQB);
            $sError = $oQS->buildQuickSearch();

            if(!empty($sError))
              return json_encode(array('alert' => $sError));

            $asHTML = $this->_getCompanyList($oQB);

            return json_encode($oPage->getAjaxExtraContent(array('data' => convertToUtf8($asHTML['data']),
                'action' => 'goPopup.removeActive(\'layer\'); initHeaderManager(); ')));
            break;

          case CONST_ACTION_SEARCH:

            if(getValue('complex_search'))
            {
              $this->csType = getValue('data_type');
              $oSearch = CDependency::getComponentByName('search');
              $oQB = $oSearch->buildComplexSearchQuery();

              $asError = $oSearch->getError();
              if(!empty($asError))
                return json_encode(array('alert' => implode('<br />', $asError)));

              $asHTML = $this->_getCompanyList($oQB);
              $asHTML['data'] = convertToUtf8($asHTML['data']);
              return json_encode($oPage->getAjaxExtraContent($asHTML));
            }

            return $this->_autocompleteCompany();
            break;
        }
        break;


      case CONST_CANDIDATE_TYPE_FEED:

        if(empty($this->cnPk))
          return json_encode(array('alert' => 'No company to search news about'));

        $asData = array('sl_candidatepk' => (int)getValue('sl_candidatepk', 0), 'companyfk' => $this->cnPk);
        $this->_updateCompanyRss($this->cnPk);
        $asFeed = $this->_getCompanyFeedTab($asData);
        return json_encode(array('data' => $asFeed['content'], 'action' => '$(\'.aTabContent:visible\').mCustomScrollbar(\'update\');'));
        break;

      case CONST_CANDIDATE_TYPE_MEETING:

         switch($this->csAction)
        {
          case CONST_ACTION_VIEW:
            return json_encode($this->_oPage->getAjaxExtraContent(array('data' => $this->_getCandidateMeetingHistory($this->cnPk))));
            break;

          case CONST_ACTION_LIST:
            return json_encode($this->_oPage->getAjaxExtraContent(array('data' => $this->_getConsultantMeeting())));
            break;

          case CONST_ACTION_ADD:
          case CONST_ACTION_EDIT:
            $nMeetingPk = (int)getValue('meetingpk');
            return json_encode($this->_oPage->getAjaxExtraContent($this->_getCandidateMeetingForm($this->cnPk, $nMeetingPk)));
            break;

          case CONST_ACTION_DONE:
            $nMeetingPk = (int)getValue('meetingpk');
            return json_encode($this->_oPage->getAjaxExtraContent(array('data' => $this->_getMeetingDoneForm($this->cnPk, $nMeetingPk))));
            break;

          case CONST_ACTION_SAVEADD:
            return json_encode($this->_saveMeeting($this->cnPk));
            break;

          case CONST_ACTION_SAVEEDIT:
            return json_encode($this->_updateMeeting($this->cnPk, true));
            break;

          case CONST_ACTION_VALIDATE:
            return json_encode($this->_updateMeetingDone($this->cnPk));
            break;
        }
        break;

        case CONST_CANDIDATE_TYPE_CONTACT:

        switch($this->csAction)
        {
          case CONST_ACTION_ADD:
            return json_encode($this->_oPage->getAjaxExtraContent(array('data' => $this->_getCandidateContactForm($this->cnPk))));
            break;

          case CONTACT_ADD:
            return json_encode($this->_oPage->getAjaxExtraContent(array('data' => $this->_getCandidateContactForm($this->cnPk,0,"add"))));
            break;

          case CONTACT_EDIT:
            return json_encode($this->_oPage->getAjaxExtraContent(array('data' => $this->_getCandidateContactForm($this->cnPk,0,"edit"))));
            break;

          case CONST_ACTION_SAVEADD:
            return json_encode($this->_getCandidateContactSave($this->cnPk));
            break;
        }
        break;

        case CONST_CANDIDATE_TYPE_CONTACT_SHOW:

        switch($this->csAction)
        {
          case CONST_ACTION_ADD:
            return json_encode($this->_oPage->getAjaxExtraContent(array('data' => $this->_getCandidateContactForm($this->cnPk))));
            break;
        }
        break;

      case CONST_CANDIDATE_TYPE_DOC:

        switch($this->csAction)
        {
          case CONST_ACTION_VIEW:
            return json_encode($this->_oPage->getAjaxExtraContent($this->_getViewLastDocument($this->cnPk)));
            break;

          case CONST_ACTION_ADD:
            return json_encode($this->_oPage->getAjaxExtraContent(array('data' => $this->_getResumeAddForm($this->cnPk))));
            break;

          case CONST_ACTION_SAVEADD:
            return json_encode($this->_getResumeSaveAdd($this->cnPk));
            break;
        }
        break;

      case CONST_CANDIDATE_TYPE_RM:

        switch($this->csAction)
        {
          case CONST_ACTION_VIEW:
            return json_encode($this->_oPage->getAjaxExtraContent($this->_getRmList($this->cnPk)));
            break;

          case CONST_ACTION_DELETE:
            return json_encode($this->_cancelCandidateRm($this->cnPk));
            break;

          case CONST_ACTION_ADD:
            return json_encode($this->_addCandidateRm($this->cnPk));
            break;

          case CONST_ACTION_EDIT:
            return json_encode($this->_extendCandidateRm($this->cnPk));
            break;

        }
        break;

      case CONST_CANDIDATE_TYPE_LOGS:

        return json_encode($this->_oPage->getAjaxExtraContent($this->_getMoreLogs($this->cnPk)));
        break;


      /* ******************************************** */
      /* ******************************************** */
      // Automcplete fields
      case CONST_CANDIDATE_TYPE_INDUSTRY:
      case CONST_CANDIDATE_TYPE_OCCUPATION:
        switch($this->csAction)
        {
          case CONST_ACTION_SEARCH:
            return $this->_autocompleteSearch($this->csType);
            break;
        }
        break;
    }
  }

  public function deleteSelectedCompany()
  {
    $old_company_id = $_GET['cidS'];
    $new_company_id = $_GET['newId'];

    if($old_company_id > 0 && $new_company_id > 0)
    {
      findRelatedCompanies($old_company_id,$new_company_id);

      $oLogin = CDependency::getCpLogin();
      $user_id = $oLogin->getUserPk();

      $loginfk = $user_id;
      $cp_pk = $old_company_id;
      $text = "Company #".$old_company_id." merged with company #".$new_company_id;
      $table = "user_history";
      $desctiption = '';
      $cp_type = "comp";

      insertLog($loginfk, $old_company_id, $text,$table,$desctiption,$cp_type);// ikisinede yazmamiz istendi
      insertLog($loginfk, $new_company_id, $text,$table,$desctiption,$cp_type);// ikisinede yazmamiz istendi

      $html = "Company deleted / merged succesfully...";
    }
    else
    {
      $html = "Please select a company";
    }

    return $html;
  }

  private function _deleteCompany()
  {
    //$oPage = CDependency::getCpPage();
    $data = array();
    $company_id = $_GET['cid'];

    $this->_oPage->addJsFile(self::getResourcePath().'js/candidate_form.js');
    $this->_oPage->addJsFile('/component/form/resources/js/currency.js');
    $this->_oPage->addJsFile(array('/component/form/resources/js/jquery.bsmselect.js',
      '/component/form/resources/js/jquery.bsmselect.sortable.js','/component/form/resources/js/jquery.bsmselect.compatibility.js'));

    $this->_oPage->addCssFile(self::getResourcePath().'css/sl_candidate.css');
    $this->_oPage->addCssFile('/component/form/resources/css/jquery.bsmselect.css');
    $this->_oPage->addCssFile('/component/form/resources/css/form.css');
    $this->_oPage->addCssFile('/component/form/resources/css/token-input-mac.css');

    //DELETE_SELECTED_COMPANY
    $sURL = $this->_oPage->getAjaxUrl('555-001', DELETE_SELECTED_COMPANY, CONST_CANDIDATE_TYPE_CANDI);
    $sURL.= "&cidS=".$company_id;
    $company_information = getCompanyInformation($company_id);
    $data['company_name'] = $company_information['name'];
    $data['company_id'] = $company_id;
    $data['delete_url'] = $sURL;
    //$data['company_token_url'] = $oPage->getAjaxUrl($this->csUid, CONST_ACTION_SEARCH,CONST_CANDIDATE_TYPE_COMP, 0);

    $company_token_url = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_SEARCH, CONST_CANDIDATE_TYPE_COMP, 0);
    $data['company_token_url'] = $company_token_url;
    $html = $this->_oDisplay->render('delete_company_page', $data);

    return $html;
  }

  //remove if the interface is not used
  public function getHtml()
  {
    $this->_processUrl();

    //================================================================
    //================================================================
    //Going to have a few generic pages requiring "addPageStructure", so it's here

    if(getValue('contact_sheet'))
    {
      $oLogin = CDependency::getComponentByName('login');
      $this->_oPage->addJsFile(self::getResourcePath().'js/sl_candidate.js');
      return $oLogin->displayList(false);
    }


    switch($this->csType)
    {
      case CONST_CANDIDATE_TYPE_CANDI:

        switch($this->csAction)
        {
          case CONST_ACTION_LIST:
            return $this->_displayCandidateList();
            break;

          case CONST_ACTION_ADD:
          case CONST_ACTION_EDIT:
            return $this->_getCandidateAddForm($this->cnPk);
            break;


          case CONST_ACTION_VIEW:
            /*//load an empty tab with a js to load the candidate
            $sURL = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, $this->cnPk);
            $sHTML = 'Candidate #'.$this->cnPk.'

              <script>view_candi("'.$sURL.'");</script>';
            return addPageStructure($sHTML, 'candi');*/
            $_POST['candidate'] = $this->cnPk;
            return mb_convert_encoding($this->_getCandidateList(), 'utf8');
            break;
        }
        break;


      case CONST_CANDIDATE_TYPE_MEETING:

        switch($this->csAction)
        {
          case CONST_ACTION_SAVEEDIT:
            $asResult = $this->_updateMeeting($this->cnPk);

            if(isset($asResult['error']))
              return $this->_oDisplay->getErrorMessage($asResult['error'], true);

            return $this->_oDisplay->getBlocMessage($asResult['data'], true);

            break;

          case CONST_ACTION_EDIT:

            $asResult = $this->_getCandidateMeetingForm($this->cnPk);
            if(isset($asResult['error']) && !empty($asResult['error']))
              return $this->_oDisplay->getErrorMessage($asResult['error'], true);

            return $this->_oDisplay->getBlocMessage($asResult['data'], true);
            break;
        }
        break;


        case CONST_CANDIDATE_TYPE_COMP:
          switch($this->csAction)
          {
            case CONST_ACTION_LIST:
              return $this->_getNoScoutList();
              break;

            case CONST_ACTION_VIEW:
              return $this->_getCompanyView($this->cnPk);
              break;
          }
          break;


        case CONST_CANDIDATE_TYPE_USER:
          $oLogin = CDependency::getCpLogin();
          return $oLogin->displayUserList(true, '');
          break;
    }
  }


  //==> cron interface 1 fct
  //remove if the interface is not used
  public function getCronJob()
  {
    $nHour = (int)date('H');
    echo 'SL_Candidate cron job | Hr: '.$nHour.'<br />
      &update_rss_feed=1 for Company rss feed<br />
      &update_currency=1 for Currency listing<br /><br />
      &update_profile_rating=1 for quality profile<br />
      &rm_notification=1 for send email for expiring rm (usually at 7am)<br /><br />';

    if(getValue('update_rss_feed'))
    {
      $this->_updateCompanyRss();
    }

    if(getValue('update_profile_rating'))
    {
       $this->updateCandidateProfiles();
    }

    //$this->_updateRm();
    if(getValue('update_currency')) // Calisirsa gunluk cron joblari buraya yazabiliriz...
    {
      //cronjob test mail
      //$sDate = date('Y-m-d H:i:s');
      //$to = 'munir@slate-ghc.com';
      //$subject = 'Cronjob test';
      //$message = 'Cronjob test '.$sDate;
      //sendHtmlMail($to,$subject, $message);
      //cronjob test mail
      require_once('component/sl_candidate/resources/currency/update_currency.php5');
    }

    $nHour = (int)date('H');
    $bForceNotify = (bool)getValue('rm_notification');
    if($nHour == 7 || $bForceNotify)
    {
      $oSetting = CDependency::getComponentByName('settings');
      $sLastUpdate = $oSetting->getSettingValue('cron_rm_notification');
      if(!$bForceNotify && $sLastUpdate > date('Y-m-d'))
      {
        echo 'already launched on RM '.$sLastUpdate;
      }
      else
      {
        $oSetting->setSystemSettings('cron_rm_notification', date('Y-m-d H:i:s'));
        $this->_manageRmExpiration($bForceNotify);
      }
    }

    return '';
  }


  //==> has settings interface 2 fcts
  public function declareSettings()
  {
    return array();
  }


  public function declareUserPreferences()
  {
    $asPrefs[] = array(
        'fieldname' => 'candidate_tabs',
        'fieldtype' => 'select',
        'options' => array('full' => '1 block - Vertical tabs', 'fullH' => '1 block - Horizontal tabs', 'half' => '2 blocks - Vertical tabs', 'halfH' => '2 blocks - Horizontal tabs'),
        'label' => 'Tabs display option',
        'description' => 'Define if the candidates and companies tabs are displayed in 1 or 2 sections',
        'value' => 'half'
    );

    $asPrefs[] = array(
        'fieldname' => 'candidate_template',
        'fieldtype' => 'select',
        'options' => array('default_candidate' => 'Slistem 2.6', 'candidate_sl3' => 'Slistem 3'),
        'label' => 'Template for candidate profile',
        'description' => 'Choose a template to display the candidates details',
        'value' => 'default_candidate'
    );

    $asPrefs[] = array(
        'fieldname' => 'qs_wide_search',
        'fieldtype' => 'select',
        'options' => array('0' => 'No', '1' => 'Yes'),
        'label' => 'Use wide search by default',
        'description' => 'Use wide search by default (firstname%, lastname%)',
        'value' => '0'
    );

    $asPrefs[] = array(
        'fieldname' => 'qs_name_order',
        'fieldtype' => 'select',
        'options' => array('lastname' => 'Lastname, Firstname', 'firstname' => 'Firstname, Lastname', 'none' => 'Indifferent'),
        'label' => 'Order in the name field',
        'description' => 'Order of the name in QS field',
        'value' => 'lastname'
    );

    $asPrefs[] = array(
        'fieldname' => 'candi_list_field',
        'fieldtype' => 'select_multi',
        'options' => array('Age' => 'date_birth', 'Salary' => 'salary', 'Managed by' => 'manager', 'Last note' => 'note', 'Title' => 'title', 'Department' => 'department'),
        'label' => 'Field to display in the list',
        'description' => 'Select the fields you want tosee in the candidate list',
        'value' => '',
        'multiple' => 1
    );

    $asPrefs[] = array(
        'fieldname' => 'candi_salary_format',
        'fieldtype' => 'select',
        'options' => array('' => 'Raw format 1,000,000¥', 'K' => 'Kilo 1,000 K¥', 'M' => 'Million 1 M¥'),
        'label' => 'Salary format',
        'description' => 'Choose what format to use to input salary and bonus values',
        'value' => '',
        'multiple' => 1
    );


    return $asPrefs;
  }


  //==> search interface 3 fcts
  public function getSearchFields($psType = '', $pbAdvanced = false)
  {
    $asFields = array();

    if($pbAdvanced)
    {
      //keep the file somwhere else, gonna be big
      require_once($_SERVER['DOCUMENT_ROOT'].self::getResourcePath().'conf/field_description.inc.php5');

      $oLogin = CDependency::getCpLogin();
      $user_id = $oLogin->getUserPk();

      $complex_search_counts = getAILogsCount("complex_search",$user_id);

      if(isset($complex_search_counts) && !empty($complex_search_counts))
      {
        foreach ($complex_search_counts as $key => $value)
        {
          $asFields[CONST_CANDIDATE_TYPE_CANDI][$value['data']]['display']['group'] = "*MOST_USED";
        }
      }

    }
    else
    {
      $asFields = array(
          CONST_CANDIDATE_TYPE_CANDI => array(
              'table' => 'sl_candidate',
              'custom_url' => 'google.com',
              'label' => 'Candidates',
              'fields' => array(
                  'text' => array('firstname', 'lastname', 'occupation', 'industry', 'note')
                  )
              )
          );
    }

    return $asFields;
  }

  public function getSearchResultMeta($psType = '')
  {
    $sURL = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_SEARCH, $psType);
    $asResultMeta = array('custom_result_page' => $sURL,
    'onBeforeSubmit' =>
      ' var asContainer = goTabs.create(\''.$psType.'\', \'\', true, \''.ucfirst($psType).' search\');
        AjaxRequest(\''.$sURL.'\', \'body\', \'searchFormId\', asContainer[\'id\']);
        return true; ');

    //AjaxRequest(\''.$sURL.'\', \'\', \'searchFormId\', asContainer[\'id\'], \'\', \'\', \'goPopup.removeActive(\\\'layer\\\'); \');

    return $asResultMeta;
  }

  public function getSearchResult($psDatatype, $psKeywords, $psFieldType = 'all', $pnDisplayPage=0)
  {
    $oPage = CDependency::getCpPage();
    $sURL = $oPage->getAjaxUrl($this->csUid, CONST_ACTION_LIST, $psDatatype);

    $asResult = array();
    $asResult['custom_result']['script'] = '
      var asContainer = goTabs.create(\''.$psDatatype.'\', \'\', \'\', \'search result\');
      AjaxRequest(\''.$sURL.'\', \'\', \'\', asContainer[\'id\']);
      goPopup.removeActive(\'layer\');
      ';

    $asResult['custom_result']['html'] = 'Loading ... ';
    return $asResult;
  }



  //notification_item => 1 function

  /**
   * Return an array that MUST contain 4 fields: label. description, url, link
   * @param variant $pvItemPk (integer or array of int)
   * @param string $psAction
   * @param string $psItemType
   * @return array of string
  */
  public function getItemDescription($pvItemPk, $psAction = '', $psItemType = 'candi')
  {
    ChromePhp::log('getItemDescription 3');
    if(!assert('is_arrayOfInt($pvItemPk) || is_key($pvItemPk)'))
      return array();

    if(!assert('!empty($psItemType)'))
      return array();

    $oLogin = CDependency::getCpLogin();

    switch($psItemType)
    {
      case 'candi':

        $asCandidate = $this->_getModel()->getCandidateData($pvItemPk, true, true);
        if(empty($asCandidate))
          return array();

        $asItem = array();
        foreach($asCandidate as $nPk => $asData)
        {
          $asItem[$nPk]['label'] = $this->_getCandidateNameFromData($asData);
          $asItem[$nPk]['url'] = $this->_oPage->getUrl($this->csUid, CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, $nPk);
          $asItem[$nPk]['link'] = $this->_oDisplay->getLink($asItem[$nPk]['label'], $asItem[$nPk]['url'], array('target' => '_blank'));
          $asItem[$nPk]['link_popup'] = $this->_oDisplay->getLink($asItem[$nPk]['label'], 'javascript:;', array('onclick' => 'popup_candi(this, \''.$asItem[$nPk]['url'].'&pg=ajx\'); '));
          $asItem[$nPk]['status'] = $asData['statusfk'];

          $asDesc = array();
          $asDesc[0] = 'RefId : #<a href="'.$asItem[$nPk]['url'].'">'.$nPk.'</a>';
          $asDesc[1] = '';

          if(!empty($asData['company_name']))
            $asDesc[1].= 'Working at '.$asData['company_name'];

          if(!empty($asData['department']))
            $asDesc[1].= '  |  department: '.$asData['department'];

          if(!empty($asData['title']))
            $asDesc[1].= '  |  as : '.$asData['title'];

          $asDesc[2] = '';
          if(!empty($asData['industry']))
            $asDesc[2] = 'Industry: '.$asData['industry'];

          if(!empty($asData['occupation']))
          {
            if(!empty($asDesc[2]))
              $asDesc[2].= '  |  ';

            $asDesc[2].= 'Occupation: '.$asData['occupation'];
          }

          $asDesc[3] = 'Created on the '.$asData['date_added'].' by '.$oLogin->getUserLink((int)$asData['created_by']);
          $asItem[$nPk]['description'] = implode('<br />', $asDesc);
        }

        break;

      case 'comp':

        if(is_integer($pvItemPk))
          $sPk = ' = '.$pvItemPk;
        else
          $sPk = ' IN('.implode(',', $pvItemPk).') ';

        $oDbResult = $this->_getModel()->getByWhere('sl_company', 'sl_companypk '. $sPk);
        $bRead = $oDbResult->readFirst();
        if(!$bRead)
          return array();

        $asItem = array();
        while($bRead)
        {
          $nPk = (int)$oDbResult->getFieldValue('sl_companypk');

          $asItem[$nPk]['label'] = '#'.$nPk.' - '.$oDbResult->getFieldValue('name');

          $asItem[$nPk]['description'] = $oDbResult->getFieldValue('description');
          $asItem[$nPk]['description'].= '<br />Created on the '.$oDbResult->getFieldValue('date_created').' by '.$oLogin->getUserLink((int)$oDbResult->getFieldValue('created_by'));


          $asItem[$nPk]['url'] = $this->_oPage->getUrl($this->csUid, CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_COMP, $nPk);
          $asItem[$nPk]['link'] = $this->_oDisplay->getLink($asItem[$nPk]['label'], $asItem[$nPk]['url']);
          $asItem[$nPk]['link_popup'] = $this->_oDisplay->getLink($asItem[$nPk]['label'], 'javascript:;', array('onclick' => 'popup_candi(this, \''.$asItem[$nPk]['url'].'&pg=ajx\'); '));

          $bRead = $oDbResult->readNext();
        }

        break;

      default:
        assert('false; // unknown type');
        return array();
        break;
    }

    return $asItem;
  }

  //------------------------------------------------------
  //  System and cached function (industry, location, occupations ...
  //------------------------------------------------------





  private function _getCandidateNameFromData($pasData)
  {
    if(empty($pasData))
      return '';

    return (($pasData['sex'] == 1)? 'Mr ': 'Ms ').$pasData['lastname'].' '.$pasData['firstname'];
  }

  //====================================================================
  //  Component core
  //====================================================================




    //------------------------------------------------------
    //  Private methods
    //------------------------------------------------------

    private function _displayCandidateList($pbInAjax = false)
    {

      $this->_oPage->addCssFile(self::getResourcePath().'css/sl_candidate.css');
      $this->_oPage->addJsFile(self::getResourcePath().'js/sl_candidate.js');
      $sHTML = $this->_getTopPageSection();

      $sLiId = uniqid();
      if(!$pbInAjax)
        $this->_oPage->addCustomJs('$(document).ready(function(){  initHeaderManager(); goTabs.preload(\'candi\', \''.$sLiId.'\', true); });');


      //container in which we'll put the list
      $sHTML.=  $this->_oDisplay->getBlocStart('', array('id' => 'bottomCandidateSection', 'class' => 'bottomCandidateSection'));
      $sHTML.=  $this->_oDisplay->getListStart('tab_content_container');

        $sHTML.=  $this->_oDisplay->getListItemStart($sLiId);

          //$sHTML.= $this->_oDisplay->getBlocStart(uniqid(), array('class' => 'scrollingContainer'));
          $sHTML.= $this->_getCandidateList($pbInAjax);
          //$sHTML.= $this->_oDisplay->getBlocEnd();

        $sHTML.=  $this->_oDisplay->getListItemEnd();

      $sHTML.=  $this->_oDisplay->getListEnd();
      $sHTML.=  $this->_oDisplay->getBlocEnd();
      return $sHTML;
    }


    private function _getTopPageSection()
    {
      $nItemPk = (int)getValue('slpk', 0);
      $sItemType = getValue('sltype');

      if(!empty($nItemPk))
      {
        if(empty($sItemType))
          $sItemType = CONST_CANDIDATE_TYPE_CANDI;

        $sContent = $this->_getItemTopSection($sItemType, $nItemPk);
        $sClass = '';
      }
      else
      {
        $sContent = '';
        $sClass = ' hidden ';
      }

      return $this->_oDisplay->getBloc('', $sContent, array('id' => 'topCandidateSection', 'class' => 'topCandidateSection'.$sClass));
    }



    private function _getItemTopSection($psItemType, $pnItemPk)
    {
      if(!assert('!empty($psItemType) && is_key($pnItemPk)'))
        return '';

      switch($psItemType)
      {
        case CONST_CANDIDATE_TYPE_CANDI:
          return $this->_getCandidateView($pnItemPk);
          break;

        case CONST_CANDIDATE_TYPE_COMP:
          return $this->_getCompanyView($pnItemPk);
          break;

        case CONST_CANDIDATE_TYPE_POS:
          return $this->_getPositionView($pnItemPk); // or call position component
          break;

      }

      return __LINE__.' - Nothing to display.';
    }

    public function mailAjax()
    {
      $oLogin = CDependency::getCpLogin();

      $candidate_id = $_GET['ppk'];
      $company_id = $_GET['cid'];
      $user_id = $oLogin->getUserPk();

      $checkCount = checkSecurityAlert($user_id, 'contact_mail',$company_id);
      if($checkCount > 0)
      {
        $checkFlag = false;
      }
      else
      {
        $checkFlag = true;
      }

      if(isset($company_id) && !empty($company_id) && $company_id > 0 && $user_id > 0 && $checkFlag)
      {
        addSecutrityAlert($user_id,'contact_mail',$company_id);

        $company_information = getCompanyInformation($company_id);
        $creator_id = $company_information['created_by'];
        $owners = getCompanyOwner($company_id);
        $toEmail = '';

        //$owners[]['owner'] = $creator_id;
        if(!isset($owners[0]['owner']))
        {
          $owners[0]['owner'] = '343';// kimse yoksa rossana
        }
        $ownerFlag = false;
        foreach ($owners as $key => $owner)
        {
          if($user_id == $owner['owner'])
          {
            $ownerFlag = true;
          }
        }

        if($ownerFlag)
        {
          #ChromePhp::log('NO MAIL!!');
          #do nothing
        }
        else
        {
          foreach ($owners as $key => $value)
          {
            $owner_id =  $value['owner'];
            $candidate_information = getCandidateInformation($candidate_id);
            $company_information = getCompanyInformation($company_id);
            $user_information = getUserInformaiton($user_id);

            $creator_information = getUserInformaiton($owner_id);// owner a aticaz
            if($creator_information['status'] == 1)
            {
              $toEmail .= $creator_information['email'].";";
            }
            else
            {// eleman aktif degilse Rosasna ya gonderiyoruz...
              if (strpos($toEmail, 'rkiyamu@slate.co.jp') !== false)
              {
                  #rossana varsa birdaha ekleme
              }
              else
              {
                $toEmail .= 'rkiyamu@slate.co.jp;';
              }

            }

          }
          $toEmail = rtrim($toEmail, ";");

          $sDate = date('Y-m-d H:i:s');

          $user_name = $user_information['firstname']." ".$user_information['lastname'];
          $candidate_name = $candidate_information['firstname']." ".$candidate_information['lastname'];
          $company_name = $company_information['name'];

          $subject = "Contact Information Access";
          $message = $user_name." (#".$user_id.") has accessed the contact information of ".$candidate_name." (#".$candidate_id."), who works at ".$company_name." (#".$company_id.") Date: ".$sDate;


          sendHtmlMail($toEmail,$subject, $message);

        }
      }

    }

    public function logAjax()
    {
      $oLogin = CDependency::getCpLogin();

      $candidate_id = $_GET['ppk'];
      $logType = $_GET['logType'];
      $user_id = $oLogin->getUserPk();

      if($logType == "candiTab2")
      {
        $text = "Contacts viewed";
        securityCheckContactView($user_id);
      }
      else if($logType == "candiTab3")
      {
        $text = "Documents viewed";
      }
      else if($logType == "candiTab7")
      {
        $text = "Company history viewed";
      }
      else
      {
        $text = "Candidate viewed";
      }

      insertLog($user_id, $candidate_id, $text, "user_history");

    }

    private function _getCandidateView($pnPk, $pasRedirected = array())
    {
      //$searchID = $_GET['searchId'];
      if(isset($_GET['searchId']))
      {
        $searchID = $_GET['searchId'];

        $pbInAjax = 'search_'.$searchID;
        return $this->_displayCandidateList($pbInAjax);
      }

      if(!assert('is_key($pnPk)'))
        return '';

      $sHTML = '';

      //-----------------------------------------------------------------------
      //check the candidate profile and update _has_doc, in_play, quality_ratio
      if(getValue('check_profile'))
      {
        $asCandidate = $this->updateCandidateProfile($pnPk);
      }

      $sViewURL = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, $pnPk);
      if(getValue('preview'))
      {
        $sHTML.= $this->_oDisplay->getBloc('', '
          <a href="javascript:;" class="candi-pop-link" onclick="goPopup.removeAll(true); view_candi(\''.$sViewURL.'\');">close <b>all</b> popops & view in page<img src="/component/sl_candidate/resources/pictures/goto_16.png" /></a>
          ', array('class' => 'close_preview'));
      }

      $asCandidate = $this->_getModel()->getCandidateData($pnPk, true);
      if(!empty($asCandidate['_sys_redirect']))
      {
        $oRight = CDependency::getComponentByName('right');
        if(!$oRight->canAccess($this->csUid, 'sys_dba', CONST_CANDIDATE_TYPE_CANDI))
          return $this->_getCandidateView((int)$asCandidate['_sys_redirect'], $asCandidate);
      }

      //converting language attributes
      if(isset($asCandidate['attribute']['candi_lang']))
      {
        $asLanguage = $this->getVars()->getLanguageList();
        foreach($asCandidate['attribute']['candi_lang'] as $nKey => $nLanguageFk)
              $asCandidate['attribute']['candi_lang'][$nKey] = $asLanguage[$nLanguageFk];
      }

      if(empty($asCandidate))
      {
        return $this->_oDisplay->getBlocMessage('<div class="no-candidate">
          Candidate #'.$pnPk.' not found.<br /><br />
            This candidate may have been deleted or access to its data may be restricted.<br />
            If you think it\'s an error report a bug using the link in the menu.</div>');
      }

      //----------------------------------------------------------------------
      //fetch other data that are not in the candidate table
      //TODO: same queries/functions used again when creatign tabs ...
      $asCandidate['rm'] = $this->_getModel()->getCandidateRm($pnPk);
      $asCandidate['redirected'] = $pasRedirected;



      $oPosition = CDependency::getComponentByName('sl_position');
      $asPlayFor = $oPosition->getApplication($pnPk, false, true);

      $asCandidate['in_play'] = count($asPlayFor['active']);
      set_array($asPlayFor['inactive'], 0);

      if(empty($asCandidate['in_play']))
      {
        $asCandidate['in_play'] = 0 - count($asPlayFor['inactive']);
      }
      if(empty($asCandidate['in_play']))
      {
        $asCandidate['in_play'] = 0;
      }


      $asCandidate['nb_meeting'] = 0;
      $asCandidate['date_meeting'] = '';
      $asCandidate['last_meeting'] = array('date' => '', 'status' => '');

      $oDbResult = $this->_getModel()->getByFk($pnPk, 'sl_meeting', 'candidate', '*', 'meeting_done, date_meeting');
      $bRead = $oDbResult->readFirst();
      while($bRead)
      {
        //$sMeetingDate = $oDbResult->getFieldValue('date_meeting');
        $sMeetingDate = $oDbResult->getFieldValue('date_met');
        $nStatus = (int)$oDbResult->getFieldValue('meeting_done');

        if($nStatus > 0)
        {
          if(empty($asCandidate['last_meeting']['date']) || $asCandidate['last_meeting']['date'] < $sMeetingDate)
            $asCandidate['last_meeting'] = array('date' => $sMeetingDate);
        }
        else
        {
          if(empty($asCandidate['date_meeting']) || $asCandidate['date_meeting'] > $sMeetingDate)
            $asCandidate['date_meeting'] = $sMeetingDate;
        }

        $asCandidate['last_meeting']['status'] = $nStatus;

        $asCandidate['nb_meeting']++;

        $bRead = $oDbResult->readNext();
      }

      //----------------------------------------------------------------------

      $sHTML.= $this->_oDisplay->getBlocStart('', array('class' => 'candiTopSectLeft'));
      $sHTML.= $this->_getCandidateProfile($asCandidate);

      //store a description of the current item for later use in javascript
      $sLabel = preg_replace('/[^a-z0-9 \.&]/i', ' ', $asCandidate['lastname'].' '.$asCandidate['firstname']);
      $sHTML.= $this->_oDisplay->getBloc('', '', array('class' => 'itemDataDescription hidden',
          'data-type' => 'candi',
          'data-pk' => $pnPk,
          'data-label' => $sLabel,
          'data-cp_item_selector' => '555-001|@|ppav|@|candi|@|'.$pnPk));

      $sHTML.= $this->_oDisplay->getBlocEnd();

      $sHTML.= $this->_oDisplay->getBlocStart('', array('class' => 'candiTopSectRight candiTabContainer'));
      $sHTML.= $this->_getCandidateRightTabs($asCandidate);
      $sHTML.= $this->_oDisplay->getBlocEnd();
      $sHTML.= $this->_oDisplay->getFloatHack();

      //fired before all the code is loaded ->
      //$sHTML.='<script> $(".candiTabsContent").mCustomScrollbar(); </script>';
      //a bit slow ?
      //$sHTML.='<script>$(".aTabContent").mCustomScrollbar({advanced:{updateOnContentResize: true}}); </script>';


      $sLink = 'javascript: view_candi(\''.$sViewURL.'\'); ';
      $sName = $asCandidate['lastname'].' '.$asCandidate['firstname'];
      logUserHistory($this->csUid, $this->csAction, $this->csType, $this->cnPk, array('text' => 'view - '.$sName.' (#'.$pnPk.')', 'link' => $sLink));

      return $sHTML;
    }

    private function _getCandidateProfile($pasCandidateData)
    {
      if(!assert('is_array($pasCandidateData) && !empty($pasCandidateData)'))
        return '';

      $sTemplate =  $_SERVER['DOCUMENT_ROOT'].'/'.self::getResourcePath().'/template/';

      if(!empty($this->casSettings['candidate_template']))
        $sTemplate.= $this->casSettings['candidate_template'].'.tpl.class.php5';
      else
        $sTemplate.= 'default_candidate.tpl.class.php5';

      //params for the sub-templates when required
      $oTemplate = $this->_oDisplay->getTemplate($sTemplate);
      return $oTemplate->getDisplay($pasCandidateData, $this->csTplSettings);
    }

    private function _getCandidateRightTabs($pasCandidateData)
    {
      if(!assert('is_array($pasCandidateData) && !empty($pasCandidateData)'))
        return '';

      //gonna be needed for multiple tabs
      $this->casUsers = $this->_oLogin->getUserList(0, false, true);

      if($this->csTabSettings == 'full')
        return $this->_getRightTabsFull($pasCandidateData);

      if($this->csTabSettings == 'fullH')
        return $this->_getRightTabsFull($pasCandidateData, 'candiHoriSizeTabs');

      if($this->csTabSettings == 'halfH')
        return $this->_getRightTabsHalfed($pasCandidateData, 'candiHoriHalfSizeTabs', true);

      return $this->_getRightTabsHalfed($pasCandidateData);
    }

    private function _getRightTabsHalfed($pasCandidateData, $psClass = '', $pbLinkTabs = false)
    {

      $sCharSelected = $sNoteSelected = 'selected';
      $sDocSelected = $sContactSelected = $sPositionSelected = $sJdSelected = '';
      $pasCandidateData['sl_candidatepk'] = (int)$pasCandidateData['sl_candidatepk'];

      $oLogin = CDependency::getCpLogin();
      $user_id = $oLogin->getUserPk();

      $candidate_id = $pasCandidateData['sl_candidatepk'];
      //$latestFlag = getLastContactSeen($candidate_id,$user_id);

      $company_id = $pasCandidateData['companyfk']; // company client mi diye kontrol etmemiz gerekiyor.
      $company_information = getCompanyInformation($company_id);
      $is_owner = true;

      $latestFlag = checkSecurityAlert($user_id,'contact_mail',$company_id);

      if($latestFlag > 0)
      {
        $is_owner = false;
      }

      $owners = getCompanyOwner($company_id);

      foreach ($owners as $key => $value)
      {
        if(isset($value['owner']) && $value['owner'] == $user_id)
        {
          $is_owner = false;
        }
      }

      if($company_information['is_client'] == 1 && $is_owner)
      {
        $company_id_flag = $pasCandidateData['companyfk'];
      }
      else
      {
        $company_id_flag = 'false';
      }

      // fetch the content of each tab first. Tab selection, or specific actions may come from that
      $oNotes = CDependency::getComponentByName('sl_event');
      $asCharNotes = $oNotes->displayNotes($pasCandidateData['sl_candidatepk'], CONST_CANDIDATE_TYPE_CANDI, 'character', array(), true, 'character');
      if(empty($asCharNotes['nb_result']))
      {
        //$sCharSelected = '';
        //$sContactSelected = 'selected';
        $asCharNotes['nb_result'] = '';
      }
      else
        $asCharNotes['nb_result'] = '<span class="tab_number tab_level_1">'.$asCharNotes['nb_result'].'</span>';

      $asContact = $this->_getContactTab($pasCandidateData);
      if(empty($asContact['nb_result']))
      {
         //$sContactSelected = '';
        (empty($sCharSelected))? $sDocSelected = 'selected' : '';
        $asContact['nb_result'] = '';
      }
      else
        $asContact['nb_result'] = '<span class="tab_number tab_level_1">'.$asContact['nb_result'].'</span>';

      $asNotes = $oNotes->displayNotes($pasCandidateData['sl_candidatepk'], CONST_CANDIDATE_TYPE_CANDI, '', array('character', 'cp_history', 'cp_hidden'), true, 'note');
      if(empty($asNotes['nb_result']))
      {
        //$sNoteSelected = '';
        //$sJdSelected = 'selected';
        $asNotes['nb_result'] = '';
      }
      else
        $asNotes['nb_result'] = '<span class="tab_number tab_level_1">'.$asNotes['nb_result'].'</span>';

      $asDocument = $this->_getDocumentTab($pasCandidateData);
      if(empty($asDocument['nb_result']))
      {
        $asDocument['nb_result'] = '';
      }
      else
        $asDocument['nb_result'] = '<span class="tab_number tab_level_1">'.$asDocument['nb_result'].'</span>';

      $asPosition = $this->_getPositionTab($pasCandidateData);
      if(empty($asPosition['nb_result']))
      {
        $asPosition['nb_result'] = '';
      }
      else
        $asPosition['nb_result'] = '<span class="tab_number tab_level_1">'.$asPosition['nb_result'].'</span>';

      $asCpHistory = $oNotes->displayNotes($pasCandidateData['sl_candidatepk'], CONST_CANDIDATE_TYPE_CANDI, 'cp_history', array(), false);
      if(empty($asCpHistory['nb_result']))
      {
        $asCpHistory['nb_result'] = '';
      }
      else
        $asCpHistory['nb_result'] = '<span class="tab_number tab_level_1">'.$asPosition['nb_result'].'</span>';


      $asCompanyFeed = $this->_getCompanyFeedTab($pasCandidateData);
      $asActivity = $this->_getRecentActivity($pasCandidateData['sl_candidatepk']);
      $sActionTab = $this->_getActionTab($pasCandidateData);


      //manage tab height by adding halfSize class. Full size by defaut
      if(empty($psClass))
        $psClass = 'candiHalfSizeTabs';

      $sHTML = $this->_oDisplay->getBlocStart('', array('class' => $psClass.' candiRightTabsContainer'));

        $sHTML.= $this->_oDisplay->getListStart('', array('class' => 'candiTabsVertical'));
          $sHTML.= '<li id="tabLink0" onclick="toggleCandiTab(this, \'candiTab0\', \'#ctc_1\');" class="tabActionLink tab_action" title="All the actions to be done on a candidate"></li>';
          $sHTML.= '<li id="tabLink1" onclick="toggleCandiTab(this, \'candiTab1\', \'#ctc_1\');" class="tab_character '.$sCharSelected.'" title="Displays the character notes" >'.$asCharNotes['nb_result'].'</li>';
          $sHTML.= '<li id="tabLink2" onclick="toggleCandiTab(this, \'candiTab2\', \'#ctc_1\','.$pasCandidateData['sl_candidatepk'].','.$company_id_flag.' );" class="tab_contact '.$sContactSelected.' title="Displays the contact details">'.$asContact['nb_result'].'</li>';
          $sHTML.= '<li id="tabLink3" onclick="toggleCandiTab(this, \'candiTab3\', \'#ctc_1\');" class="tab_document '.$sDocSelected.'" title="Displays the uploaded documents">'.$asDocument['nb_result'].'</li>';
          $sHTML.= '<li id="tabLink4" onclick="toggleCandiTab(this, \'candiTab4\', \'#ctc_1\');" class="tab_company" title="Displays the company news feed"></li>';
        $sHTML.= $this->_oDisplay->getListEnd();

        if($pbLinkTabs)
        {
          $sHTML.= $this->_oDisplay->getListStart('', array('class' => 'candiTabsVertical'));
          $sHTML.= '<li id="tabLink5" onclick="toggleCandiTab(this, \'candiTab5\', \'#ctc_2\');" class="tab_note '.$sNoteSelected.'" title="Displays notes">'.$asNotes['nb_result'].'</li>';
          $sHTML.= '<li id="tabLink6" onclick="toggleCandiTab(this, \'candiTab6\', \'#ctc_2\');" class="tab_activity" title="Displays the recent activity of this candidate"></li>';
          $sHTML.= '<li id="tabLink7" onclick="toggleCandiTab(this, \'candiTab7\', \'#ctc_2\');" class="tab_history" title="Displays the company history">'.$asCpHistory['nb_result'].'</li>';
          $sHTML.= '<li id="tabLink8" onclick="toggleCandiTab(this, \'candiTab8\', \'#ctc_2\');" class="tab_position '.$sJdSelected.'" title="Displays the positions & applications">'.$asPosition['nb_result'].'</li>';
          $sHTML.= $this->_oDisplay->getListEnd();
        }

        $sHTML.= $this->_oDisplay->getBlocStart('ctc_1', array('class' => 'candiTabsContent'));
          $sHTML.= $this->_oDisplay->getBloc('candiTab0', $sActionTab, array('class' => 'aTabContent hidden '));
          $sHTML.= $this->_oDisplay->getBloc('candiTab1', $asCharNotes['content'], array('class' => 'aTabContent hidden '.$sCharSelected));
          $sHTML.= $this->_oDisplay->getBloc('candiTab2', $asContact['content'], array('class' => 'aTabContent hidden '.$sContactSelected));
          $sHTML.= $this->_oDisplay->getBloc('candiTab3', $asDocument['content'], array('class' => 'aTabContent hidden '.$sDocSelected));
          $sHTML.= $this->_oDisplay->getBloc('candiTab4', $asCompanyFeed['content'], array('class' => 'aTabContent hidden'));
        $sHTML.= $this->_oDisplay->getBlocEnd();

      $sHTML.= $this->_oDisplay->getBlocEnd();

      //separator
      if($psClass == 'candiHoriHalfSizeTabs')
        $sHTML.= $this->_oDisplay->getBloc('', '&nbsp;', array('class' => 'candiTabsSeparator Htabs'));
      else
        $sHTML.= $this->_oDisplay->getBloc('', '&nbsp;', array('class' => 'candiTabsSeparator '));

      $sHTML.= $this->_oDisplay->getBlocStart('ctc_2', array('class' => $psClass.' candiRightTabsContainer'));

        if(!$pbLinkTabs)
        {
          $sHTML.= $this->_oDisplay->getListStart('', array('class' => 'candiTabsVertical'));
          $sHTML.= '<li id="tabLink5" onclick="toggleCandiTab(this, \'candiTab5\', \'#ctc_2\');" class="tab_note '.$sNoteSelected.'" title="Displays notes">'.$asNotes['nb_result'].'</li>';
          $sHTML.= '<li id="tabLink6" onclick="toggleCandiTab(this, \'candiTab6\', \'#ctc_2\');" class="tab_activity" title="Displays the recent activity of this candidate"></li>';
          $sHTML.= '<li id="tabLink7" onclick="toggleCandiTab(this, \'candiTab7\', \'#ctc_2\');" class="tab_history" title="Displays the company history">'.$asCpHistory['nb_result'].'</li>';
          $sHTML.= '<li id="tabLink8" onclick="toggleCandiTab(this, \'candiTab8\', \'#ctc_2\');" class="tab_position '.$sJdSelected.'" title="Displays the positions & applications">'.$asPosition['nb_result'].'</li>';
          $sHTML.= $this->_oDisplay->getListEnd();
        }

        $sHTML.= $this->_oDisplay->getBlocStart('', array('class' => 'candiTabsContent'));
          $sHTML.= $this->_oDisplay->getBloc('candiTab5', $asNotes['content'], array('class' => 'aTabContent hidden '.$sNoteSelected));
          $sHTML.= $this->_oDisplay->getBloc('candiTab6', $asActivity['content'], array('class' => 'aTabContent hidden'));
          $sHTML.= $this->_oDisplay->getBloc('candiTab7', $asCpHistory['content'], array('class' => 'aTabContent hidden'));
          $sHTML.= $this->_oDisplay->getBloc('candiTab8', $asPosition['content'], array('class' => 'aTabContent hidden '.$sJdSelected));
        $sHTML.= $this->_oDisplay->getBlocEnd();

      $sHTML.= $this->_oDisplay->getBlocEnd();


      return $sHTML;
    }

    private function _getRightTabsFull($pasCandidateData, $psClass = '')
    {
      $pasCandidateData['sl_candidatepk'] = (int)$pasCandidateData['sl_candidatepk'];

      $oLogin = CDependency::getCpLogin();
      $user_id = $oLogin->getUserPk();

      $candidate_id = $pasCandidateData['sl_candidatepk'];
      //$latestFlag = getLastContactSeen($candidate_id,$user_id);

      $company_id = $pasCandidateData['companyfk']; // company client mi diye kontrol etmemiz gerekiyor.
      $company_information = getCompanyInformation($company_id);
      $is_owner = true;

      $latestFlag = checkSecurityAlert($user_id,'contact_mail',$company_id);

      if($latestFlag)
      {
        $is_owner = false;
      }

      $owners = getCompanyOwner($company_id);

      foreach ($owners as $key => $value)
      {
        if(isset($value['owner']) && $value['owner'] == $user_id)
        {
          $is_owner = false;
        }
      }

      if($company_information['is_client'] == 1 && $is_owner)
      {
        $company_id_flag = $pasCandidateData['companyfk'];
      }
      else
      {
        $company_id_flag = 'false';
      }

      $sHTML = "";

      $sCharSelected =  'selected';
      $sDocSelected = $sContactSelected = $sJdSelected = $sNoteSelected = '';
      $pasCandidateData['sl_candidatepk'] = (int)$pasCandidateData['sl_candidatepk'];

      // fetch the content of each tab first. Tab selection, or specific actions may come from that
      $oNotes = CDependency::getComponentByName('sl_event');
      $asCharacter = $oNotes->displayNotes($pasCandidateData['sl_candidatepk'], CONST_CANDIDATE_TYPE_CANDI, 'character', array(), true, 'character');

      if(empty($asCharacter['nb_result']))
      {
        //$sCharSelected = '';
        //$sNoteSelected = 'selected';
        $asCharNotes['nb_result'] = '';
      }

      $asNotes = $oNotes->displayNotes($pasCandidateData['sl_candidatepk'], CONST_CANDIDATE_TYPE_CANDI, '', array('character', 'cp_history', 'cp_hidden'), true, 'note');
      if(empty($asNotes['nb_result']))
      {
        $sNoteSelected = '';
        (empty($sCharSelected))? $sContactSelected = 'selected' : '';
      }

      $asContact = $this->_getContactTab($pasCandidateData);

      if(empty($asContact['nb_result']))
      {
        $sContactSelected = '';
        (empty($sCharSelected) && empty($sNoteSelected))? $sDocSelected = 'selected' : '';
      }

      $asDocument = $this->_getDocumentTab($pasCandidateData);
      if(empty($asDocument['nb_result']))
      {
        $sDocSelected = '';
        (empty($sCharSelected) && empty($sNoteSelected) && empty($sContactSelected))? $sJdSelected = 'selected' : '';
      }

      $asCompanyFeed = $this->_getCompanyFeedTab($pasCandidateData);

      $asActivity = $this->_getRecentActivity($pasCandidateData['sl_candidatepk']); //HATA BURADA
      $asPosition = $this->_getPositionTab($pasCandidateData);
      $sActionTab = $this->_getActionTab($pasCandidateData);

      $asCpHistory = $oNotes->displayNotes($pasCandidateData['sl_candidatepk'], CONST_CANDIDATE_TYPE_CANDI, 'cp_history', array(), false);

      $nTotalData = $asCharacter['nb_result'] + $asNotes['nb_result'] + $asContact['nb_result'] +
              $asDocument['nb_result'] +$asPosition['nb_result'] + $asCpHistory['nb_result'];

      if(empty($nTotalData))
      {
        $sJdSelected = '';
        $sActionTabSelected = ' selected';
      }
      else
        $sActionTabSelected = '';

      if(empty($psClass))
        $psClass = 'candiFullSizeTabs';


      $sHTML = $this->_oDisplay->getBlocStart('', array('class' => $psClass.' candiRightTabsContainer'));
        $sHTML.= $this->_oDisplay->getListStart('', array('class' => 'candiTabsVertical'));

          $sHTML.= '<li id="tabLink0" onclick="toggleCandiTab(this, \'candiTab0\');" class="tabActionLink tab_action'.$sActionTabSelected.'" title="All the actions to be done on a candidate" />&nbsp;</li>';

          if($asCharacter['nb_result'] > 0)
            $sHTML.= '<li id="tabLink1" onclick="toggleCandiTab(this, \'candiTab1\');" class="'.$sCharSelected.' tab_character" title="Displays the character notes" ><span class="tab_number tab_level_'.$asCharacter['priority'].'">'.$asCharacter['nb_result'].'</span></li>';
          else
            $sHTML.= '<li id="tabLink1" onclick="toggleCandiTab(this, \'candiTab1\');" class="tab_empty '.$sCharSelected.' tab_character" title="Displays the character notes" /></li>';

          if($asNotes['nb_result'] > 0)
            $sHTML.= '<li id="tabLink11" onclick="toggleCandiTab(this, \'candiTab5\');" class="'.$sNoteSelected.' tab_note" title="Displays the candidate notes" ><span class="tab_number tab_level_'.$asNotes['priority'].'">'.$asNotes['nb_result'].'</span></li>';
          else
            $sHTML.= '<li id="tabLink11" onclick="toggleCandiTab(this, \'candiTab5\');" class="tab_empty '.$sNoteSelected.' tab_note" title="Displays the candidate notes" ></li>';

          if($asContact['nb_result'] > 0)
            $sHTML.= '<li id="tabLink2" onclick="toggleCandiTab(this, \'candiTab2\',\'\','.$pasCandidateData['sl_candidatepk'].','.$company_id_flag.' );" class="'.$sContactSelected.' tab_contact" title="Displays the contact details"><span class="tab_number tab_level_'.$asContact['priority'].'">'.$asContact['nb_result'].'</span></li>';
          else
            $sHTML.= '<li id="tabLink2" onclick="toggleCandiTab(this, \'candiTab2\',\'\','.$pasCandidateData['sl_candidatepk'].','.$company_id_flag.' );" class="tab_empty '.$sContactSelected.' tab_contact" title="Displays the contact details"></li>';

          if($asDocument['nb_result'] > 0)
            $sHTML.= '<li id="tabLink3" onclick="toggleCandiTab(this, \'candiTab3\',\'\','.$pasCandidateData['sl_candidatepk'].');" class="'.$sDocSelected.' tab_document" title="Displays the uploaded documents"><span class="tab_number tab_level_'.$asDocument['priority'].'">'.$asDocument['nb_result'].'</span></li>';
          else
            $sHTML.= '<li id="tabLink3" onclick="toggleCandiTab(this, \'candiTab3\',\'\','.$pasCandidateData['sl_candidatepk'].');" class="tab_empty '.$sDocSelected.' tab_document" title="Displays the uploaded documents"></li>';

          if($asPosition['nb_result'] > 0)
            $sHTML.= '<li id="tabLink8" onclick="toggleCandiTab(this, \'candiTab8\');" class="'.$sJdSelected.' tab_position title="Displays the positions & applications"><span class="tab_number tab_level_'.$asPosition['priority'].'">'.$asPosition['nb_result'].'</span></li>';
          else
            $sHTML.= '<li id="tabLink8" onclick="toggleCandiTab(this, \'candiTab8\');" class="tab_empty '.$sJdSelected.' tab_position" title="Displays the positions & applications"></li>';


          $sHTML.= '<li id="tabLink4" onclick="toggleCandiTab(this, \'candiTab4\');" class="tab_empty tab_company" title="Displays the company news"></li>';
          $sHTML.= '<li id="tabLink6" onclick="toggleCandiTab(this, \'candiTab6\');" class="tab_empty tab_activity" title="Displays the recent activity of this candidate"></li>';

          if($asCpHistory['nb_result'] > 0)
            $sHTML.= '<li id="tabLink7" onclick="toggleCandiTab(this, \'candiTab7\',\'\','.$pasCandidateData['sl_candidatepk'].');" class="tab_history" title="Displays the company history"><span class="tab_number">'.$asCpHistory['nb_result'].'</span></li>';
          else
            $sHTML.= '<li id="tabLink7" onclick="toggleCandiTab(this, \'candiTab7\',\'\','.$pasCandidateData['sl_candidatepk'].');" class="tab_empty tab_history" title="Displays the company history"></li>';

        $sHTML.= $this->_oDisplay->getListEnd();


        $sHTML.= $this->_oDisplay->getBlocStart('', array('class' => 'candiTabsContent'));
          $sHTML.= $this->_oDisplay->getBloc('candiTab0', $sActionTab, array('class' => 'aTabContent hidden '.$sActionTabSelected));
          $sHTML.= $this->_oDisplay->getBloc('candiTab1', $asCharacter['content'], array('class' => 'aTabContent hidden '.$sCharSelected));
          $sHTML.= $this->_oDisplay->getBloc('candiTab5', $asNotes['content'], array('class' => 'aTabContent hidden '.$sNoteSelected));
          $sHTML.= $this->_oDisplay->getBloc('candiTab2', $asContact['content'], array('class' => 'aTabContent hidden '.$sContactSelected));
          $sHTML.= $this->_oDisplay->getBloc('candiTab3', $asDocument['content'], array('class' => 'aTabContent hidden '.$sDocSelected));
          $sHTML.= $this->_oDisplay->getBloc('candiTab4', $asCompanyFeed['content'], array('class' => 'aTabContent hidden'));

          $sHTML.= $this->_oDisplay->getBloc('candiTab6', $asActivity['content'], array('class' => 'aTabContent hidden'));
          $sHTML.= $this->_oDisplay->getBloc('candiTab7', $asCpHistory['content'], array('class' => 'aTabContent hidden'));
          $sHTML.= $this->_oDisplay->getBloc('candiTab8', $asPosition['content'], array('class' => 'aTabContent hidden '.$sJdSelected));

        $sHTML.= $this->_oDisplay->getBlocEnd();
      $sHTML.= $this->_oDisplay->getBlocEnd();


      return $sHTML;
    }


    private function _getActionTab($pasCandidateData)
    {
      if(!assert('is_array($pasCandidateData) && !empty($pasCandidateData)'))
        return '';

      $oPage = CDependency::getCpPage();
      $asItem = array('cp_uid' => '555-001', 'cp_action' => CONST_ACTION_VIEW, 'cp_type' => CONST_CANDIDATE_TYPE_CANDI, 'cp_pk' => $pasCandidateData['sl_candidatepk']);

      $sHTML = $this->_oDisplay->getBlocStart('', array('class' => 'candi_action_tab'));
      $sHTML.= $this->_oDisplay->getListStart();

        $sURL = $oPage->getAjaxUrl('sl_candidate', CONST_ACTION_EDIT, CONST_CANDIDATE_TYPE_CANDI, $pasCandidateData['sl_candidatepk']);
        $sJavascript = 'var oConf = goPopup.getConfig(); oConf.width = 1080; oConf.height = 725;  goPopup.setLayerFromAjax(oConf, \''.$sURL.'\'); ';
        $sHTML.= '<li><a href="javascript:;" onclick=" '.$sJavascript.'"><img title="Edit candidate" src="/component/sl_candidate/resources/pictures/tabs/character_24.png"> Edit candidate</a></li>';


        $sURL = $oPage->getAjaxUrl('sl_candidate', CONST_ACTION_ADD, CONST_CANDIDATE_TYPE_CANDI, $pasCandidateData['sl_candidatepk']);
        $sJavascript = 'var oConf = goPopup.getConfig(); oConf.width = 1080; oConf.height = 725;  goPopup.setLayerFromAjax(oConf, \''.$sURL.'\'); ';
        $sHTML.= '<li><a href="javascript:;" onclick=" '.$sJavascript.'"><img title="Edit candidate" src="/component/sl_candidate/resources/pictures/duplicate_24.png"> Duplicate candidate</a></li>';

        $sURL = $oPage->getAjaxUrl('sl_event', CONST_ACTION_ADD, CONST_EVENT_TYPE_EVENT, 0, $asItem);
        $sJavascript = 'var oConf = goPopup.getConfig(); oConf.width = 950; oConf.height = 550;  goPopup.setLayerFromAjax(oConf, \''.$sURL.'\'); ';
        $sHTML.= '<li><a href="javascript:;" onclick="$(\'#tabLink5\').click(); '.$sJavascript.'"><img src="/component/sl_candidate/resources/pictures/tabs/note_24.png" title="Add notes"/> Add notes or character notes</a></li>';

        $sURL = $oPage->getAjaxUrl('sl_candidate', CONST_ACTION_ADD, CONST_CANDIDATE_TYPE_CONTACT, $pasCandidateData['sl_candidatepk'], $asItem);
        $sJavascript = 'var oConf = goPopup.getConfig(); oConf.width = 950; oConf.height = 750;  goPopup.setLayerFromAjax(oConf, \''.$sURL.'\'); ';
        $sHTML.= '<li><a href="javascript:;" onclick=" '.$sJavascript.'"><img src="/component/sl_candidate/resources/pictures/tabs/contact_24.png" title="Add/edit contact details"/> Add contact details</a></li>';

        $sURL = $oPage->getAjaxUrl('sharedspace', CONST_ACTION_ADD, CONST_SS_TYPE_DOCUMENT, 0, $asItem);
        $sJavascript = 'var oConf = goPopup.getConfig(); oConf.width = 950; oConf.height = 550;  goPopup.setLayerFromAjax(oConf, \''.$sURL.'\'); ';
        $sHTML.= '<li><a href="javascript:;" onclick="$(\'#tabLink3\').click(); '.$sJavascript.'"><img src="/component/sl_candidate/resources/pictures/tabs/document_24.png" title="Upload documents"/> Upload a document</a></li>';

        $sURL = $oPage->getAjaxUrl('sl_candidate', CONST_ACTION_ADD, CONST_CANDIDATE_TYPE_DOC, 0, $asItem);
        $sJavascript = 'var oConf = goPopup.getConfig(); oConf.width = 1000; oConf.height = 750;  goPopup.setLayerFromAjax(oConf, \''.$sURL.'\'); ';
        $sHTML.= '<li><a href="javascript:;" onclick="'.$sJavascript.'"><img src="/component/sl_candidate/resources/pictures/create_doc_24.png" title="Create a new resume"/> Create a resume</a></li>';

        $sURL = $oPage->getAjaxUrl('555-005', CONST_ACTION_ADD, CONST_POSITION_TYPE_LINK, 0, array('candidatepk' => $pasCandidateData['sl_candidatepk']));
        $sJavascript = 'var oConf = goPopup.getConfig(); oConf.width = 950; oConf.height = 550;  goPopup.setLayerFromAjax(oConf, \''.$sURL.'\'); ';
        $sHTML.= '<li><a href="javascript:;" onclick="$(\'#tabLink8\').click(); '.$sJavascript.'"><img src="/component/sl_candidate/resources/pictures/tabs/position_24.png" title="Set in play"/> Set in play for a new position</a></li>';

        $sURL = $oPage->getAjaxUrl('sl_candidate', CONST_ACTION_ADD, CONST_CANDIDATE_TYPE_MEETING, $pasCandidateData['sl_candidatepk']);
        $sJavascript = 'var oConf = goPopup.getConfig(); oConf.width = 950; oConf.height = 550;  goPopup.setLayerFromAjax(oConf, \''.$sURL.'\'); ';
        $sHTML.= '<li><a href="javascript:;" onclick=" '.$sJavascript.'"><img title="New meeting" src="/component/sl_candidate/resources/pictures/calendar_24.png"> Set up a meeting</a></li>';

        $sURL = $oPage->getAjaxUrl('sl_candidate', CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_RM, $pasCandidateData['sl_candidatepk']);
        $sJavascript = 'var oConf = goPopup.getConfig(); oConf.width = 600; oConf.height = 400;  goPopup.setLayerFromAjax(oConf, \''.$sURL.'\'); ';
        $sHTML.= '<li><a href="javascript:;" onclick=" '.$sJavascript.'"><img title="Set up a meeting with this candidate" src="/component/sl_candidate/resources/pictures/calendar_24.png"> RM list</a></li>';



        if($this->_oLogin->isAdmin())
        {
          $sURL = $oPage->getAjaxUrl('sl_candidate', CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, $pasCandidateData['sl_candidatepk'], array('check_profile' => 1));
          $sHTML.= '<li><a href="javascript:;" onclick="view_candi(\''.$sURL.'\'); ">
            <img title="Set up a meeting with this candidate" src="/component/sl_candidate/resources/pictures/admin_24.png">
            Check profile</a></li>';

        }

       $sHTML.= $this->_oDisplay->getListEnd();
       $sHTML.= $this->_oDisplay->getBlocEnd();
       return $sHTML;
    }

    private function _getContactTab($pasCandidateData)
    {
      if(!assert('is_array($pasCandidateData) && !empty($pasCandidateData)'))
        return array();

      $oPage = CDependency::getCpPage();
      $asTypeTitle = array( 1 => 'Home phone number', 2 => 'Office phone number', 3 => 'Website url', 4 => 'Fax number',
                            5 => 'Email address', 6 => 'Mobile phone number', 7 => 'LinkedIn url', 8 => 'Facebook url', 9 => 'info', 10 => 'Skype address');

      ///in case there's no group
      if(!isset($this->casUserData['group']))
        $this->casUserData['group'] = array();

      $oDbResult = $this->_getModel()->getContact($pasCandidateData['sl_candidatepk'], 'candi', $this->casUserData['pk'], array_keys($this->casUserData['group']), true);
      $bRead = $oDbResult->readFirst();
      $nCount = 0;
      $nPriority = 0;
      $sHTML = '';
      $bRmMasked = false;
      $is_creator = false;

      if ($pasCandidateData['creatorfk'] == $this->casUserData['loginpk'])
        $is_creator = true;

      //if there's a RM, we hide contact details
      if(!empty($pasCandidateData['rm']) && !isset($pasCandidateData['rm'][$this->casUserData['loginpk']])
              /*&& $this->_isActiveConsultant($pasCandidateData['managerfk'])*/)
      {
        if(isset($_SESSION['sl_candidate']['contact_acccess'][$pasCandidateData['sl_candidatepk']]))
          $nAccess = $_SESSION['sl_candidate']['contact_acccess'][$pasCandidateData['sl_candidatepk']];
        else
          $nAccess = 0;

        //Once clicked and RM notified, we grant access to contact details for 1 hour
        if($nAccess < (time() - 3600))
        {
          $bRmMasked = true;
          $nManagers = count($pasCandidateData['rm']);
          if($nManagers == 1)
          {
            foreach($pasCandidateData['rm'] as $asRm)
              $sManager = $asRm['link'].' is this candidate RM';
          }
          else
            $sManager = 'there are '.$nManagers.' RMs';

          $sURL = $oPage->getAjaxUrl($this->csUid, CONST_ACTION_LOG, CONST_CANDIDATE_TYPE_CANDI, $pasCandidateData['sl_candidatepk']);
          $sHTML.= $this->_oDisplay->getBlocStart('', array('class' => 'notice1 clickable', 'style' => '',  'onclick' => 'AjaxRequest(\''.$sURL.'\'); $(this).parent().find(\'.toggle_contact\').toggle(); $(this).fadeOut(function(){ remove(); }); '));
          $sHTML.= 'Notice: '.$sManager.'. Click here to display the contact information.';
          $sHTML.= $this->_oDisplay->getBlocEnd();
          $sHTML.= $this->_oDisplay->getFloatHack();
        }
      }


      $sHTML.= "<table>
                  <tr>
                    <td style='width:300px; padding-left:100px;'>";
                      $sHTML.= $this->_oDisplay->getBlocStart('', array('class' => 'tab_bottom_link'));
                      $sURL = $oPage->getAjaxUrl('sl_candidate', CONTACT_ADD, CONST_CANDIDATE_TYPE_CONTACT, (int)$pasCandidateData['sl_candidatepk']);
                      $sJavascript = 'var oConf = goPopup.getConfig(); oConf.width = 950; oConf.height = 750;  goPopup.setLayerFromAjax(oConf, \''.$sURL.'\'); ';
                      $sHTML.= '<a href="javascript:;" onclick=" '.$sJavascript.'">Add new contact</a>';
                      $sHTML.= $this->_oDisplay->getBlocEnd();
      $sHTML.= "    </td>
                    <td style='width:300px; padding-right:100px;'>";
                      $sHTML.= $this->_oDisplay->getBlocStart('', array('class' => 'tab_bottom_link'));
                      $sURL = $oPage->getAjaxUrl('sl_candidate', CONTACT_EDIT, CONST_CANDIDATE_TYPE_CONTACT, (int)$pasCandidateData['sl_candidatepk']);
                      $sJavascript = 'var oConf = goPopup.getConfig(); oConf.width = 950; oConf.height = 750;  goPopup.setLayerFromAjax(oConf, \''.$sURL.'\'); ';
                      $sHTML.= '<a href="javascript:;" onclick=" '.$sJavascript.'">Edit contacts</a>';
                      $sHTML.= $this->_oDisplay->getBlocEnd();

      $sHTML.= "    </td>
                  </tr>
                </table>";

      if($bRead)
      {
        $sAMonthAgo = date('Y-m-d H:i:s', strtotime('-1 month'));
        $sTwoMonthAgo = date('Y-m-d H:i:s', strtotime('-2 month'));
        $sHTML.= $this->_oDisplay->getListStart('', array('class' => 'contactList'));

        //Warning about recently contacted candidates
        $asDate = array(
            (empty($pasCandidateData['date_added']))? 0: strtotime($pasCandidateData['date_created']),
            (empty($pasCandidateData['date_updated']))? 0: strtotime($pasCandidateData['date_updated']),
            (empty($pasCandidateData['date_met']))? 0: strtotime($pasCandidateData['date_met']),
            (empty($pasCandidateData['last_meeting']['date']))? 0: strtotime($pasCandidateData['last_meeting']['date'])
        );

        $sLastUpdate = date('Y-m-d', max($asDate));
        if($sLastUpdate > $sAMonthAgo)
        {
          if($pasCandidateData['date_added'] > $sAMonthAgo)
          {
            $sHTML.= '<div class="contact_warning">Candidate created on the '.$sLastUpdate.' </div>';
          }
          elseif($pasCandidateData['date_updated'] > $sAMonthAgo)
          {
            $sHTML.= '<div class="contact_warning">Candidate updated on the '.$sLastUpdate.' </div>';
          }
          else
            $sHTML.= '<div class="contact_warning">Candidate met on the '.$sLastUpdate.' </div>';


        }

        while($bRead)
        {
          $asData = $oDbResult->getData();
          if(empty($asData['value']))
            $asData['value'] = '-';

          if(empty($asData['date_update']))
          {
            $sDate = $asData['date_create'];
            $nLoginFk = (int)$asData['creatorfk'];
          }
          else
          {
            $sDate = $asData['date_update'];
            $nLoginFk = (int)$asData['updated_by'];
          }

          if($sDate > $sTwoMonthAgo)
            $nPriority = 2;
          elseif($sDate > $sAMonthAgo)
            $nPriority = 1;

          if($nLoginFk > 0)
          {
            if($nLoginFk == $this->casUserData['pk'])
            {
              $sUser = 'by me';
              $sUserName = 'me';
            }
            else
            {
              $sUser = 'by '.$this->_oLogin->getUserLink((int)$nLoginFk, true);
              $sUserName = $this->_oLogin->getUserName((int)$nLoginFk, true);
            }
          }
          else
            $sUser = '';

          $sItem = $this->_oDisplay->getBloc('', '&nbsp;', array('class' => 'contactIcon contact_type'.$asData['type'], 'title' => $asTypeTitle[$asData['type']]));

          $bVisible = $this->check_contact_info_visibility($asData, $this->casUserData, $is_creator);

          if(!$bRmMasked && $bVisible || $asData['visibility'] == 1)
          {
            switch($asData['type'])
            {
              case 3:
              case 7:
              case 8:
              case 10:

                if(preg_match('/$http/i', $asData['value']) !== 0)
                  $asData['value'] = 'http://'.$asData['value'];

                $asData['value'] = $this->_oDisplay->getLink(mb_strimwidth($asData['value'], 0, 45, '...'), $asData['value'], array('target' => '_blank'));
                break;

              case 5:
                $sCopyEmail = '<keep_to_copy_email_in_slistem_note '.$this->csUid.'__'.CONST_ACTION_VIEW.'__'.CONST_CANDIDATE_TYPE_CANDI.'__'.$pasCandidateData['sl_candidatepk'].'@slistem.slate.co.jp>';
                $sCopyEmail = urlencode($sCopyEmail);
                $asData['value'] = $this->_oDisplay->getLink($asData['value'], 'javascript:;', array('onclick' => 'window.open(\'mailto:'.$asData['value'].'?bcc='.$sCopyEmail.'\', \'zm_mail\');'));
                break;
            }

            $sItem.= $this->_oDisplay->getBloc('', '&nbsp;'.$asData['value'], array('class' => 'contactData'));
          }
          else
          {

            if($bVisible)
            {
              $sMaskedValue = substr($asData['value'], 0, 5).'<span class="contact_masked_symbol"> ';
              for($nLetter = 0; $nLetter < (strlen($asData['value']) -5); $nLetter++)
                $sMaskedValue.= '&#9679;';

              $sMaskedValue.= '</span>';
              $sValueString = $this->_oDisplay->getSpan('', $sMaskedValue, array('class' => 'toggle_contact contact_masked'));
              $sValueString.= $this->_oDisplay->getSpan('', $asData['value'], array('class' => 'toggle_contact hidden'));
            }
            else
            {
              $sValueString = $this->_oDisplay->getSpan('', ' -=[ private ]=- ', array('class' => 'toggle_contact contact_masked'));
            }


            $sItem.= $this->_oDisplay->getBloc('', '&nbsp;'.$sValueString, array('class' => 'contactData'));
          }


          if(empty($asData['date_update']))
            $sItem.= $this->_oDisplay->getBloc('','<em>added '.date('Y-M-d', strtotime($sDate)).'</em><br />'.$sUser, array('class' => 'contactDate'));
          else
            $sItem.= $this->_oDisplay->getBloc('','<em>updated '.date('Y-M-d', strtotime($sDate)).'</em><br />'.$sUser, array('class' => 'contactDate'));

          if($bVisible)
          {
            if(!empty($asData['description']))
              $sItem.= $this->_oDisplay->getBloc('', $asData['description'], array('class' => 'contactDescription'));
            else
              $sItem.= $this->_oDisplay->getBloc('', '<em class="text_small">no description</em>', array('class' => 'contactDescription'));
          }
          else
            $sItem.= $this->_oDisplay->getBloc('', '<em class="text_small">Ask
              <a href="javascript:;" onclick="
              var oConf = goPopup.getConfig();
              oConf.height = 500;
              oConf.width = 850;
              goPopup.setLayerFromAjax(oConf, \''.CONST_CRM_DOMAIN.'/index.php5?uid=333-333&ppa=ppaa&ppt=msg&ppk=0&loginfk='.$nLoginFk.'&pg=ajx\'); " >'.$sUserName.'</a> if you need to access this.</em>', array('class' => 'contactDescription'));


          $sHTML.= $this->_oDisplay->getListItem($sItem);
          $bRead = $oDbResult->readNext();
          $nCount++;
        }
      }
      else
      {
        $sHTML .= '<div class="entry"><div class="note_content"><em>No contact details.</em></div></div>';
      }

      return array('content' => $sHTML, 'nb_result' => $nCount, 'priority' => $nPriority);
    }

    private function check_contact_info_visibility($candidate_contact, $current_user_data, $is_creator)
    {

      $visible = false;

      if ($current_user_data['admin'])
      {
        $visible = true;
      }
      else if ($is_creator)
      {
        $visible = true;
      }
      else if ($candidate_contact['visibility'] == 1)
      {
        $visible = true;
      }
      else if ($candidate_contact['creatorfk'] == $current_user_data['pk'])
      {
        $visible = true;
      }
      else if (!empty($candidate_contact['custom_visibility']))
      {
        $user_list = explode(',', $candidate_contact['custom_visibility']);

        if (in_array($current_user_data['pk'], $user_list))
        {
          $visible = true;
        }
      }

      return $visible;
    }

    /** return the lastest update feed obout the candidate company
     *  $pasCandidateData must contain at least the candidate pk [sl_candidatepk] and [companyfk]
     *  if there's no rss feed data included in the array, the function will fetch it
     *
     * @param array $pasCandidateData
     * @return array
     */
    private function _getCompanyFeedTab($pasCandidateData)
    {
      if(!assert('is_array($pasCandidateData) && !empty($pasCandidateData)'))
        return array();

      if(empty($pasCandidateData['companyfk']))
      {
        $sHTML = '<div class="floathack" />';
        $sHTML.= '<div class="tab_bottom_link">No company to search news about</div>';
        return array('content' => $sHTML, 'nb_result' => 0);
      }

      $oPage = CDependency::getCpPage();

      $sURL = $oPage->getAjaxUrl($this->csUid, CONST_ACTION_EDIT, CONST_CANDIDATE_TYPE_FEED, (int)$pasCandidateData['companyfk']);
      $sURL.= '&sl_candidatepk='.(int)$pasCandidateData['sl_candidatepk'];
      $sURL.= '&companyfk='.(int)$pasCandidateData['companyfk'];

      $sId = 'company_feed_'.$pasCandidateData['companyfk'];
      //$sJavascript = $this->_oDisplay->getAjaxJs($sURL, '', '', $sId, '', '', '$(this).closest(\'.aTabContent\').mCustomScrollbar(\'update\');');
      $sJavascript = $this->_oDisplay->getAjaxJs($sURL, '', '', $sId);

      //check if the data is in the candidate data or if I have to get it (ajax)
      if(isset($pasCandidateData['sl_company_rsspk']))
      {
        $asFeed = $pasCandidateData;
        $asFeed['date_created'] = $asFeed['date_rss'];
      }
      else
      {
        $oDbResult = $this->_getModel()->getFeedByCompanyFk((int)$pasCandidateData['companyfk']);
        $bRead = $oDbResult->readFirst();

        if($bRead)
          $asFeed = $oDbResult->getData();
        else
          $asFeed = array('nb_news' => 0);
      }

      $sHTML = $this->_oDisplay->getBlocStart($sId);
      if(!empty($asFeed['date_created']))
      {
        $sHTML.= 'Last updated the '.$asFeed['date_created'].'<br/><br/>';
        $sHTML.= $asFeed['content'];


        $sHTML.= '<div class="floathack" />';
        $sHTML.= '<div class="tab_bottom_link">';
        $sHTML.= '<a href="javascript:;" onclick="'.$sJavascript.'">Update feed now</a> <br />';
        $sHTML.= 'More news available <a href="https://www.google.com/search?tbm=nws&q='.urlencode($asFeed['company_name']).'" target="_blank"> here </a>';
        $sHTML.= '</div>';
      }
      else
      {
        $sHTML.= '<div class="floathack" />';
        $sHTML.= '<div class="tab_bottom_link">';
        $sHTML.= 'No result yet, <a href="javascript:;" onclick="'.$sJavascript.'">search now</a> ?  <br />';
        $sHTML.= 'Or search straight on <a href="https://www.google.com/search?tbm=nws&q='.urlencode($asFeed['company_name']).'" target="_blank">google news</a>';
        $sHTML.= '</div>';
      }

      $sHTML.= $this->_oDisplay->getBlocEnd();
      return array('content' => $sHTML, 'nb_result' => (int)$asFeed['nb_news']);
    }


    /** Return the user activity based on the system logs
     *
     * @param array $pasCandidateData
     * @return array html + nb results
     */
    private function _getRecentActivity($pnPk, $psType = '', $pnPage = 1)
    {
      if(!assert('is_key($pnPk)'))
        return array();

      $nActivityToDisplay = 25;
      $skip_activity = array('upd sl_candidate_profile', 'upd sl_candidate', 'upd sl_position_link', 'upd sl_candidate_rm',
        'upd settings_user', 'upd sl_contact', 'upd notification', 'upd notification_action', 'upd sl_meeting', 'upd notification_link',
        'upd document_link', 'upd sl_attribute', 'upd event_link', 'upd document', 'upd sl_company', 'upd folder', 'upd folder_link',
        'upd sl_position', 'upd sl_position_detail', 'upd login', 'upd document_file', 'upd revenue', 'upd login_activity',
        'upd login_system_history', 'upd sl_position_credit');

      //request 1 more activity than what is displayed to know if everything is displayed
      if($pnPage < 2)
      {
        $pnPage = 1;
        $sLimit = ($nActivityToDisplay+1);
      }
      else
        $sLimit = (($pnPage-1)*$nActivityToDisplay).','.(($pnPage*$nActivityToDisplay)+1);

      //$asComponent = array(CONST_CP_UID => $this->csUid, CONST_CP_ACTION => $this->csAction, CONST_CP_TYPE => $this->csType, CONST_CP_PK => $this->cnPk);
      $asComponent = array(CONST_CP_UID => $this->csUid, CONST_CP_ACTION => '', CONST_CP_TYPE => $psType, CONST_CP_PK => $pnPk,
          'table' => array('sl_candidate', 'document', 'sl_document', 'sl_meeting', 'position', 'user_history'),
          'uids' => array('555-001', '999-111'),
          );
      $asHistory = $this->_oLogin->getSystemHistoryItem($asComponent, $sLimit);


      $sId = 'activity_feed_'.$pnPk.'_'.$pnPage;
      $sHTML = $this->_oDisplay->getSpanStart($sId);
      $nCount = 0;

      if(empty($asHistory))
      {
        $sHTML.= 'No activity found.<br /><br />';
        $sHTML.= $this->_oDisplay->getSpanEnd();
      }
      else
      {
        foreach($asHistory as $asHistoryData)
        {
          if (in_array($asHistoryData['action'], $skip_activity))
            continue;

          if(isset($asHistoryData['userfk']) && $asHistoryData['userfk'] > 0)
          {
            $user_info = getUserInformaiton($asHistoryData['userfk']);
          }

          if(isset($user_info['phone_ext']))
          {
            $phone_ext = $user_info['phone_ext'];
          }
          else
          {
            $phone_ext = '';
          }
          $sHTML.= '<div class="entry">';
            $sHTML.= '<div class="note_header">';
            $sHTML.= '&rarr;&nbsp;&nbsp; <span>  '.$this->_oLogin->getUserLink((int)$asHistoryData['userfk'], true).' - '.$phone_ext.'</span>';
            $sHTML.= '<span class="note_date"> : '.$asHistoryData['date'].'</span>';
            $sHTML.= '</div>';

            $sHTML.= ' <div class="note_content">'.$asHistoryData['action'];

            if(!empty($asHistoryData['description']))
               $sHTML.= '<br />'.$asHistoryData['description'];

            $sHTML.= '</div>';
          $sHTML.= '</div>';

          $nCount++;
          if($nCount > $nActivityToDisplay)
            break;
        }

        $sHTML.= $this->_oDisplay->getSpanEnd();

        if(count($asHistory) > $nActivityToDisplay)
        {
          $pnPage++;

          //add an extra block to load next logs entries
          $sId = 'activity_feed_'.$pnPk.'_'.$pnPage;
          $sHTML.= $this->_oDisplay->getSpan($sId);


          $oPage = CDependency::getCpPage();
          $sURL = $oPage->getAjaxUrl($this->csUid, CONST_ACTION_LIST, CONST_CANDIDATE_TYPE_LOGS, $pnPk, array('page' => $pnPage));
          $sJavascript = $this->_oDisplay->getAjaxJs($sURL, '', '', $sId, '', '', '$(\'#tabLink6\').click(); ');

          $sHTML.= '<div class="floathack" />';
          $sHTML.= '<div class="tab_bottom_link">';
          $sHTML.= '<a href="javascript:;" onclick="'.$sJavascript.'; $(this).parent().remove();">See previous activities... </a>';
          $sHTML.= '</div>';
        }
      }

      return array('content' => $sHTML, 'nb_result' => $nCount);
    }

    private function _getMoreLogs($pnCandidatePk)
    {
      $asLogs = $this->_getRecentActivity($pnCandidatePk, CONST_CANDIDATE_TYPE_CANDI, (int)getValue('page'));
      return array('data' => $asLogs['content']);
    }

    /** Return list of document linked to the candidates
     *
     * @param array $pasItemData
     * @return array html + nb results
     */
    private function _getDocumentTab($pasItemData, $psDataType = CONST_CANDIDATE_TYPE_CANDI)
    {
      if(!assert('is_array($pasItemData) && !empty($pasItemData)'))
        return array('content' => '', 'nb_result' => 0);


      if($psDataType == CONST_CANDIDATE_TYPE_CANDI)
      {
        $nPk = (int)$pasItemData['sl_candidatepk'];
        $sTitle = $pasItemData['firstname'].' '.$pasItemData['lastname'].'\'s resume';
        $sCallback = 'refresh_candi('.$nPk.', true); ';
      }
      else
      {
        $nPk = (int)$pasItemData['sl_companypk'];
        $sTitle = 'company document ';
        $sCallback = 'refresh_comp('.$nPk.'); ';
      }

      $oPage = CDependency::getCpPage();
      $sHTML = '';

      $asItem = array(CONST_CP_UID => $this->csUid, CONST_CP_ACTION => CONST_ACTION_VIEW, CONST_CP_TYPE => $psDataType, CONST_CP_PK => $nPk);
      $oShareSpace = CDependency::getComponentByName('sharedspace');
      $asDocument = $oShareSpace->getDocuments(0, $asItem);
      $nDocument = count($asDocument);
      $nPriority = 0;

      $asItem['document_title'] = $sTitle;
      $asItem['callback'] = $sCallback;

      $sHTML.= '<div class="tab_bottom_link">';

      $sURL = $oPage->getAjaxUrl('sharedspace', CONST_ACTION_ADD, CONST_SS_TYPE_DOCUMENT, 0, $asItem);
      $sJavascript = 'var oConf = goPopup.getConfig(); oConf.width = 950; oConf.height = 550;  goPopup.setLayerFromAjax(oConf, \''.$sURL.'\'); ';
      $sHTML.= '<a href="javascript:;" onclick="'.$sJavascript.'"> Upload a document</a>';

      $sHTML.= '&nbsp;&nbsp;-&nbsp;&nbsp;';

      $sURL = $oPage->getAjaxUrl('sl_candidate', CONST_ACTION_ADD, CONST_CANDIDATE_TYPE_DOC, 0, $asItem);
      $sJavascript = 'var oConf = goPopup.getConfig(); oConf.width = 1000; oConf.height = 750;  goPopup.setLayerFromAjax(oConf, \''.$sURL.'\'); ';
      $sHTML.= '<a href="javascript:;" onclick="'.$sJavascript.'">Create a resume</a>';
      $sHTML.= '</div>';


      if($nDocument == 0)
        $sHTML.= '<div class="entry"><div class="note_content"><em>No document found.</em></div></div>';
      else
      {
        $sAMonthAgo = date('Y-m-d H:i:s', strtotime('-1 month'));
        $sTwoMonthAgo = date('Y-m-d H:i:s', strtotime('-2 month'));

        foreach($asDocument as $asDocData)
        {
          if($asDocData['date_creation'] > $sTwoMonthAgo)
            $nPriority = 2;
          elseif($asDocData['date_creation'] > $sAMonthAgo)
            $nPriority = 1;

          $sHTML.= '<div class="entry">
            <div class="note_header">
            &rarr;&nbsp;&nbsp;<span class="note_date">'.$asDocData['date_creation'].'</span>
             - <span> by '.$this->_oLogin->getUserLink($this->casUsers[$asDocData['creatorfk']], true).'</span>
            </div>
            <div class="note_content documentRow">
              <span class="doc_detail">
                <a href="javascript:;" onclick="'.$asDocData['view_popup_js'].'">details & edit</a>
              </span>

              <div class="doc_picture">
                <a href="'.$asDocData['dl_url'].'" class="" >
                <img src="'.$asDocData['icon'].'" title="'.$asDocData['mime_type'].'"/>
                  </a>
              </div>

              <div class="doc_name">
              <a href="'.$asDocData['dl_url'].'" class="" target="_blank">
                 '.$asDocData['title'].'<br />'.$asDocData['initial_name'].'
              </a>
              </div>
            </div>
          </div>';
        }
      }

      return array('content' => $sHTML, 'nb_result' => $nDocument, 'priority' => $nPriority);
    }


    private function _getPositionTab($pasCandidateData)
    {
      if(!assert('is_array($pasCandidateData) && !empty($pasCandidateData)'))
        return array('content' => '', 'nb_result' => 0);

      $oPosition = CDependency::getComponentByName('sl_position');
      $asPosition = $oPosition->getApplication($pasCandidateData['sl_candidatepk'], false, true);
      //dump($asPosition);

      $sURL = $this->_oPage->getAjaxUrl('555-005', CONST_ACTION_ADD, CONST_POSITION_TYPE_LINK, 0, array('candidatepk' => $pasCandidateData['sl_candidatepk']));
      $nPosition = $nPriority = 0;
      $sHTML = '';

      $sHTML.= '<div class="tab_bottom_link">
            <a href="javascript:;" onclick="
            oConf = goPopup.getConfig();
            oConf.height = 500;
            oConf.width = 900;
            goPopup.setLayerFromAjax(oConf, \''.$sURL.'\');">Pitch to new position</a>
        </div>';

      if(empty($asPosition['active']) && empty($asPosition['inactive']))
      {
        $sHTML .= '<div class="entry">
            <em>No application found.</em>
         </div>';
      }
      else
      {
        $oLogin = CDependency::getCpLogin();
        $asDisplayLink = array();
        foreach($asPosition['active'] as $asJdData)
        {
          if($asJdData['date_created'] > date('Y-m-d', strtotime('-1 month')));
            $nPriority = 1;

          $asJdData['link_date'] = substr($asJdData['link_date'], 0, 10);
          $asJdData['link_creator'] = $oLogin->getUserLink((int)$asJdData['link_creator'], true);

          $sHTML.= $this->_getPositionTabRow($asJdData, $pasCandidateData['sl_candidatepk']);
          $asDisplayLink[] = $asJdData['sl_positionpk'];
          $nPosition++;
        }


        //separator
        if(!empty($asPosition['inactive']))
        {

          $sHistory = '';
          foreach($asPosition['inactive'] as $asJdData)
          {
            //not display twice a position that has been re-opened
            if(!in_array($asJdData['sl_positionpk'], $asDisplayLink))
            {
              $asJdData['link_date'] = substr($asJdData['link_date'], 0, 10);
              $asJdData['link_creator'] = $oLogin->getUserLink((int)$asJdData['link_creator'], true);

              $sHistory.= $this->_getPositionTabRow($asJdData, $pasCandidateData['sl_candidatepk']);
              $nPosition++;
            }
          }

          if(!empty($sHistory))
          {
            if(!empty($asPosition['active']))
              $sHTML.= $this->_oDisplay->getBloc('', 'Inactive & expired positions', array('class' => 'position_separator'));



            $sHTML.= $sHistory;
          }
        }

      }

      return array('content' => $sHTML, 'nb_result' => $nPosition, 'priority' => $nPriority);
    }


    private function _getPositionTabRow($pasPosition, $pnCandidatePk)
    {

      $sEncoding = mb_detect_encoding($pasPosition['title']);
      //dump($pasPosition);

      //$pasPosition['title'] = mb_convert_encoding($pasPosition['title'], 'UTF-8', $sEncoding);
      if(!in_array($sEncoding, array('UTF-8', 'ASCII')))
        $pasPosition['title'].= ' [in '.$sEncoding.']';


      $sViewURL = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_COMP, (int)$pasPosition['sl_companypk']);
      $sCompany = $this->_oDisplay->getLink(mb_strimwidth($pasPosition['company_name'], 0, 55, '...'), 'javascript:;', array('class' => 'link_view', 'onclick' => 'popup_candi(this, \''.$sViewURL.'\');'));
      $sCompany.= $this->_oDisplay->getLink('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="/component/sl_candidate/resources/pictures/goto_16.png" />&nbsp;', 'javascript:;', array('onclick' => 'view_comp(\''.$sViewURL.'\');'));

      $sViewURL = $this->_oPage->getAjaxUrl('555-005', CONST_ACTION_VIEW, CONST_POSITION_TYPE_JD, (int)$pasPosition['positionfk']);
      $sPosition = $this->_oDisplay->getLink('#'.$pasPosition['sl_positionpk'].' - '.mb_strimwidth($pasPosition['title'], 0, 55, '...'), 'javascript:;', array('class' => 'link_view', 'onclick' => 'view_position(\''.$sViewURL.'\');'));

      $sViewURL = $this->_oPage->getAjaxUrl('555-005', CONST_ACTION_EDIT, CONST_POSITION_TYPE_LINK, (int)$pasPosition['sl_position_linkpk'], array('positionfk' => (int)$pasPosition['positionfk'], 'candidatefk' => $pnCandidatePk));
      $sOnclick = 'view_position(\''.$sViewURL.'\'); ';

      if($pasPosition['current_status'] <= 100)
        $sDate = 'in play since '.substr($pasPosition['date_created'], 0, 10);
      else
        $sDate = 'played until '.substr($pasPosition['date_created'], 0, 10);


      if($pasPosition['current_status'] <= 53)
      {
        $sClass = 'ontrack';  //green
      }
      elseif($pasPosition['current_status'] < 100)
      {
        $sClass = 'ontrack2 '; //blue - CCM > 3
      }
      elseif($pasPosition['current_status'] < 101)
      {
        $sClass = 'warning '; //yellow - offer
      }
      elseif($pasPosition['current_status'] == 101)
      {
        $sClass = 'placed '; //yellow - offer
      }
      elseif($pasPosition['current_status'] < 200)
      {
        $sClass = 'critical '; //red stalled - expired
      }
      else
        $sClass = 'stopped';

      $sSendCandiView = $this->_oPage->getAjaxUrl('555-005', CONST_ACTION_SEND_CLIENT, CONST_POSITION_TYPE_LINK, (int)$pasPosition['sl_position_linkpk'], array('positionfk' => (int)$pasPosition['positionfk'], 'candidatefk' => $pnCandidatePk));
      $sOnclickSendCandi = 'view_position(\''.$sSendCandiView.'\'); ';

      $sHTML = '<div class="entry">
        <div class="note_header">
        &rarr;&nbsp;&nbsp;
         position <span class="note_type">#'.$pasPosition['sl_positionpk'].'</span>
         - <span> created by '.$this->_oLogin->getUserLink($this->casUsers[$pasPosition['created_by']], true).'</span>
         <span class="note_date"><em class="light">'.$sDate.'</em></span>
        </div>
        <div class="note_content" style="margin-left: 0px; height:90px !important;">

          <div class="position_row">

            <div style="height:75px !important;" class="position_status '.$sClass.'" onclick="'.$sOnclick.'">
              <div>'.$pasPosition['status_label'].'</div>

         </div>
            <div>
              <div class="row"><div class="title">Position: </div><div class="data">'.$sPosition.'</div></div>
              <div class="row"><div class="title">Company: </div><div class="data">'.$sCompany.'</div></div>
              <div class="row"><div class="title">Update: </div><div class="data">by&nbsp;&nbsp;&nbsp;'.$pasPosition['link_creator'].'
                &nbsp;&nbsp;&nbsp;&nbsp;on the&nbsp;&nbsp;&nbsp;'.$pasPosition['link_date'].'</div></div>
              <div class="row"><div class="title">Client: </div><div class="data"><a onclick="'.$sOnclickSendCandi.'" href="#">Send to the client</a></div></div>
            </div>

            <div class="position_view" onclick="'.$sOnclick.'"><span>View & edit</span></div>

          </div>

        <div class="floatHack" />
        </div>
      </div>';

      return $sHTML;
    }


    private function _getCompanyView($pnPk)
    {
      if(!assert('is_key($pnPk)'))
        return '';

      $asCompany = $this->_getModel()->getCompanyData($pnPk, true);
      if(empty($asCompany))
        return '';

      $sHTML = '';

      $sViewURL = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_COMP, $pnPk);
      if(getValue('preview'))
      {
        $sHTML.= $this->_oDisplay->getBloc('', '
          <a href="javascript:;" class="candi-pop-link" onclick="goPopup.removeAll(true); view_comp(\''.$sViewURL.'\');">close <b>all</b> popops & view in page<img src="/component/sl_candidate/resources/pictures/goto_16.png" /></a>
          ', array('class' => 'close_preview'));
      }

      $oDbResult = $this->_getModel()->getCompanyDepartment($pnPk);
      $bRead = $oDbResult->readFirst();

      $asCompany['department'] = array();
      $asCompany['nb_employee'] = 0;
      while($bRead)
      {
        $sDepartment = $oDbResult->getFieldValue('department');
        $asCompany['nb_employee'] += (int)$oDbResult->getFieldValue('nCount');

        if(empty($sDepartment))
        {
          $sDepartment = '- Not defined - ';
          $asCompany['department'][] = '__no_department__';
          $asCompany['department_label'][] = '-- unknown -- ('.$oDbResult->getFieldValue('nCount').')';
        }
        else
        {
          $asCompany['department'][] = trim($sDepartment);
          $asCompany['department_label'][] = $sDepartment.' ('.$oDbResult->getFieldValue('nCount').')';
        }

        $bRead = $oDbResult->readNext();
      }

      //fetch data about positions and employees in play. Will be used in the view and tabs
      $oPosition = CDependency::getComponentByName('sl_position');
      $asPosition = array();
      $asPosition['jd'] = $oPosition->getCompanyPositionTabContent($pnPk);
      $asPosition['inplay'] = $oPosition->getEmployeeApplicantTabContent($pnPk, true);

      $anPositionStatus = array('critical' => $asPosition['jd']['nb_critical'], 'open' => $asPosition['jd']['nb_open'],
          'close' => $asPosition['jd']['nb_close']);

      $nApplicant = $asPosition['inplay']['nb_result'];


      $sHTML.= $this->_oDisplay->getBlocStart('', array('class' => 'candiTopSectLeft'));

        $sTemplate =  $_SERVER['DOCUMENT_ROOT'].'/'.self::getResourcePath().'/template/';

        /*if(isset($pasSettings['company_template']) && !empty($pasSettings['company_template']))
          $sTemplate.= $pasSettings['company_template'].'.tpl.class.php5';
        else
          $sTemplate.= 'default_company.tpl.class.php5';*/

        $sTemplate.= 'company_sl3.tpl.class.php5';

        //params for the sub-templates when required
        $oTemplate = $this->_oDisplay->getTemplate($sTemplate);
        $sHTML.= $oTemplate->getDisplay($asCompany, $anPositionStatus, $nApplicant);


        //store a description of the current item for later use in javascript
      $sHTML.= $this->_oDisplay->getBloc('', '', array('class' => 'itemDataDescription hidden',
          'data-type' => 'comp',
          'data-pk' => $pnPk,
          'data-label' => $asCompany['name'],
          'data-cp_item_selector' => '555-001|@|ppav|@|comp|@|'.$pnPk));

      $sHTML.= $this->_oDisplay->getBlocEnd();


      $sHTML.= $this->_oDisplay->getBlocStart('', array('class' => 'candiTopSectRight candiTabContainer'));
      $sHTML.= $this->_getCompanyRightTabs($asCompany, $asPosition);
      $sHTML.= $this->_oDisplay->getBlocEnd();
      $sHTML.= $this->_oDisplay->getFloatHack();


      $sLink = 'javascript: view_candi(\''.$sViewURL.'\'); ';
      logUserHistory($this->csUid, $this->csAction, $this->csType, $this->cnPk, array('text' => 'view - '.$asCompany['name'].' (#'.$pnPk.')', 'link' => $sLink));

      return $sHTML;
    }


    /**
     * Display all the company tabs
     *
     * @param array $pasCompany
     * @param array $pasPosition
     * @return string html content
     */
    private function _getCompanyRightTabs($pasCompany, $pasPosition)
    {
      if(!assert('is_array($pasCompany) && !empty($pasCompany)'))
        return '';

      //gonna be needed for multiple tabs
      if(empty($this->casUsers))
        $this->casUsers = $this->_oLogin->getUserList(0, false, true);


      if($this->csTabSettings == 'full')
        $sClass = 'candiFullSizeTabs';
      else
        $sClass = 'candiHoriSizeTabs';

      $sHTML = $this->_oDisplay->getBlocStart('', array('class' => $sClass.' candiRightTabsContainer'));


      $sNoteSelected = $sDocSelected = $sContactSelected = $sInPlaySelected = $sPositionSelected = $sActionSelected = '';
      $bOneSelected = false;
      $pasCompany['sl_companypk'] = (int)$pasCompany['sl_companypk'];

      // fetch the content of each tab first. Tab selection, or specific actions may come from that
      $oNotes = CDependency::getComponentByName('sl_event');
      $asNotes = $oNotes->displayNotes($pasCompany['sl_companypk'], CONST_CANDIDATE_TYPE_COMP);

      $asActivity = $this->_getRecentActivity($pasCompany['sl_companypk'], CONST_CANDIDATE_TYPE_COMP);
      $asDocument = $this->_getDocumentTab($pasCompany, CONST_CANDIDATE_TYPE_COMP);

      $pasCompany['companyfk'] = $pasCompany['sl_companypk'];
      $pasCompany['sl_candidatepk'] = 0;
      $asCompanyFeed = $this->_getCompanyFeedTab($pasCompany);

      $sAction = $this->_getCpActions($pasCompany);

      //$asIndustry = array('content' => 'Nothing there.', 'nb_result' => 0);
      //$asPosition = array('content' => 'Nothing position from this company.', 'nb_result' => 0);
      $asPosition = $pasPosition['jd'];
      $asInPlay = $pasPosition['inplay'];


      if($asNotes['nb_result'] > 0)
      {
        $bOneSelected = true;
        $sNoteSelected =  'selected';
      }

      if(!$bOneSelected && $asDocument['nb_result'] > 0)
      {
        $bOneSelected = true;
        $sNoteSelected = '';
        $sDocSelected = 'selected';
      }

      if(!$bOneSelected && $asInPlay['nb_result'] > 0)
      {
        $bOneSelected = true;
        $sDocSelected = '';
        $sInPlaySelected = 'selected';
      }

      if(!$bOneSelected && $asPosition['nb_result'] > 0)
      {
        $bOneSelected = true;
        $sInPlaySelected = '';
        $sPositionSelected = 'selected';
      }
      $sURL = $this->_oPage->getAjaxUrl('555-001', CONST_ACTION_SEARCH, CONST_CANDIDATE_TYPE_CANDI, 0, array('company' => $pasCompany['sl_companypk'], 'data_type' => CONST_CANDIDATE_TYPE_CANDI, 'qs_exact_match' => 1));
      $nDepartment = count($pasCompany['department']);
      $asDepartment = array('content' => 'No department found', 'nb_result' => $nDepartment);
      $asFirstLetter = array();
      if($nDepartment > 0)
      {
        $asDepartment['content'] = $this->_oDisplay->getBlocStart('', array('class' => 'cp_department_list'));
        foreach($pasCompany['department'] as $nKey => $sDepartment)
        {
          $sAnchor = '';

          if($nDepartment > 25 )
          {
            $sFirst = substr($sDepartment, 0, 1);
            if(!isset($asFirstLetter[$sFirst]))
            {
              $asFirstLetter[$sFirst] = '<a href="javascript:;" onclick="
                $(this).closest(\'.aTabContent\').mCustomScrollbar(\'scrollTo\', \'#dep_'.$sFirst.'\'); " >'.strtoupper($sFirst).'</a>';
              $sAnchor = 'dep_'.$sFirst;
            }
          }

          $sJavascript = '
            var asContainer = goTabs.create(\'comp\', \'\', \'\', \'Company list\');
            AjaxRequest(\''.$sURL.'&department='.urlencode($sDepartment).'\', \'body\', \'\',  asContainer[\'id\'], \'\', \'\', \'initHeaderManager(); \');
            goTabs.select(asContainer[\'number\']); ';
          $asDepartment['content'].= $this->_oDisplay->getBloc($sAnchor,  $this->_oDisplay->getLink($pasCompany['department_label'][$nKey], 'javascript:;', array('onclick' => $sJavascript)));
        }

        $asDepartment['content'].= $this->_oDisplay->getBlocEnd();

        if(count($asFirstLetter) > 0)
        {
          $asDepartment['content'] = '<div class="department_abc">'.implode('&nbsp;&nbsp;', $asFirstLetter).'</div>'. $asDepartment['content'];
        }

        $asDepartment['content'] = 'We have listed '.$nDepartment.' department(s) in this company:'.$asDepartment['content'];
        $asDepartment['nb_result'] = $nDepartment;
      }


      if(!$bOneSelected)
      {
        $sActionSelected = 'selected';
      }


      $sHTML.= $this->_oDisplay->getListStart('', array('class' => 'candiTabsVertical'));
        $sHTML.= '<li id="tabLink0" onclick="toggleCandiTab(this, \'candiTab0\');" class="'.$sActionSelected.' tabActionLink tab_action" title="All the actions to be done on a candidate"></li>';

        if($asNotes['nb_result'] > 0)
          $sHTML.= '<li id="tabLink1" onclick="toggleCandiTab(this, \'candiTab1\');" class="'.$sNoteSelected.' tab_note" title="Displays the character notes" ><span class="tab_number">'.$asNotes['nb_result'].'</span></li>';
        else
          $sHTML.= '<li id="tabLink1" onclick="toggleCandiTab(this, \'candiTab1\');" class="tab_empty '.$sNoteSelected.' tab_note" title="Displays the character notes" ></li>';

        if($asDocument['nb_result'] > 0)
          $sHTML.= '<li id="tabLink3" onclick="toggleCandiTab(this, \'candiTab3\');" class="'.$sDocSelected.' tab_document" title="Displays the uploaded documents"><span class="tab_number">'.$asDocument['nb_result'].'</span></li>';
        else
          $sHTML.= '<li id="tabLink3" onclick="toggleCandiTab(this, \'candiTab3\');" class="tab_empty '.$sDocSelected.' tab_document" title="Displays the uploaded documents"></li>';

        $sHTML.= '<li id="tabLink4" onclick="toggleCandiTab(this, \'candiTab4\');" class="tab_company tab_empty" title="Displays the company news"></li>';
        //$sHTML.= '<li id="tabLink6" onclick="toggleCandiTab(this, \'candiTab6\');" class="tab_industry tab_empty" title="Displays employee department"></li>';

        if($asDepartment['nb_result'] > 0)
          $sHTML.= '<li id="tabLink7" onclick="toggleCandiTab(this, \'candiTab7\');" class="tab_department tab_empty" title="Display company departments"><span class="tab_number">'.$asDepartment['nb_result'].'</span></li>';
        else
         $sHTML.= '<li id="tabLink7" onclick="toggleCandiTab(this, \'candiTab7\');" class="tab_department tab_empty" title="Display company departments"></li>';


        $sHTML.= '<li id="tabLink8" onclick="toggleCandiTab(this, \'candiTab8\');" class="tab_activity tab_empty" title="Displays the recent activity of this company"></li>';

        if($asPosition['nb_result'] > 0)
          $sHTML.= '<li id="tabLink9" onclick="toggleCandiTab(this, \'candiTab9\');" class="'.$sPositionSelected.' tab_job" title="Company positions"><span class="tab_number">'.$asPosition['nb_result'].'</span></li>';
        else
          $sHTML.= '<li id="tabLink9" onclick="toggleCandiTab(this, \'candiTab9\');" class="tab_job tab_empty" title="Company positions"></li>';

        if($asInPlay['nb_result'] > 0)
          $sHTML.= '<li id="tabLink10" onclick="toggleCandiTab(this, \'candiTab10\');" class="'.$sInPlaySelected.' tab_position" title="Employees in play"><span class="tab_number tab_level_1">'.$asInPlay['nb_result'].'</span></li>';
        else
          $sHTML.= '<li id="tabLink10" onclick="toggleCandiTab(this, \'candiTab10\');" class="tab_position tab_empty" title="Employees in play"></li>';

      $sHTML.= $this->_oDisplay->getListEnd();

      $sHTML.= $this->_oDisplay->getBlocStart('', array('class' => 'candiTabsContent'));
        $sHTML.= $this->_oDisplay->getBloc('candiTab0', $sAction, array('class' => 'aTabContent hidden '.$sActionSelected));
        $sHTML.= $this->_oDisplay->getBloc('candiTab1', $asNotes['content'], array('class' => 'aTabContent hidden '.$sNoteSelected));
        $sHTML.= $this->_oDisplay->getBloc('candiTab3', $asDocument['content'], array('class' => 'aTabContent hidden '.$sDocSelected));
        $sHTML.= $this->_oDisplay->getBloc('candiTab4', $asCompanyFeed['content'], array('class' => 'aTabContent hidden'));

        //$sHTML.= $this->_oDisplay->getBloc('candiTab6', $asIndustry['content'], array('class' => 'aTabContent hidden'));
          $sHTML.= $this->_oDisplay->getBloc('candiTab7', $asDepartment['content'], array('class' => 'aTabContent hidden'));

        $sHTML.= $this->_oDisplay->getBloc('candiTab8', $asActivity['content'], array('class' => 'aTabContent hidden'));

        $sHTML.= $this->_oDisplay->getBloc('candiTab9', $asPosition['content'], array('class' => 'aTabContent hidden '.$sPositionSelected));
        $sHTML.= $this->_oDisplay->getBloc('candiTab10', $asInPlay['content'], array('class' => 'aTabContent hidden '.$sInPlaySelected));

        $sHTML.= $this->_oDisplay->getFloathack();

      $sHTML.= $this->_oDisplay->getBlocEnd();
      $sHTML.= $this->_oDisplay->getFloathack();


      $sHTML.= $this->_oDisplay->getBlocEnd();
      $sHTML.= $this->_oDisplay->getFloathack();

      return $sHTML;
    }


    private function _getCpActions($pasCompany)
    {
      $sHTML = $this->_oDisplay->getBlocStart('', array('class' => 'candi_action_tab'));
      $sHTML.= $this->_oDisplay->getListStart('', array('class' => 'candi_action_tab'));

        $sURL = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_EDIT, CONST_CANDIDATE_TYPE_COMP, $pasCompany['sl_companypk']);
        $sJavascript = 'var oConf = goPopup.getConfig(); oConf.width = 1080; oConf.height = 725;  goPopup.setLayerFromAjax(oConf, \''.$sURL.'\'); ';
        $sHTML.= $this->_oDisplay->getListItem($this->_oDisplay->getLink('Edit company', 'javascript:;', array('onclick' => $sJavascript)));

        $sURL = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_EDIT, CONST_CANDIDATE_TYPE_COMP, $pasCompany['sl_companypk']);
        $sHTML.= $this->_oDisplay->getListItem($this->_oDisplay->getLink('Add a note', $sURL));

        $sURL = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_EDIT, CONST_CANDIDATE_TYPE_COMP, $pasCompany['sl_companypk']);
        $sHTML.= $this->_oDisplay->getListItem($this->_oDisplay->getLink('Add a document', $sURL));

        $sURL = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_EDIT, CONST_CANDIDATE_TYPE_COMP, $pasCompany['sl_companypk']);
        $sHTML.= $this->_oDisplay->getListItem($this->_oDisplay->getLink('Add a position', $sURL));

        $sURL = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_EDIT, CONST_CANDIDATE_TYPE_COMP, $pasCompany['sl_companypk']);
        $sHTML.= $this->_oDisplay->getListItem($this->_oDisplay->getLink('Add a candidate', $sURL));

      $sHTML.= $this->_oDisplay->getListEnd();
      $sHTML.= $this->_oDisplay->getBlocEnd();

      return $sHTML;
    }

    /******************************************************************************************/
    /******************************************************************************************/



    /******************************************************************************************/
    /******************************************************************************************/



    /******************************************************************************************/
    /******************************************************************************************/



    private function _getCandidateList($pbInAjax = false, &$poQB = null)
    {
      if($poQB != null)
      {
        $exploded = explode('_',$poQB->getTitle());
      }
      global $gbNewSearch;
      $oDb = CDependency::getComponentByName('database');
      $this->_getModel()->loadQueryBuilderClass();
      $oLogin = CDependency::getCpLogin();

      $user_id = $oLogin->getUserPk();
      securityCheckSearch($user_id);

      $asListMsg = array();
      $sTemplate = getValue('tpl');
      $bHeavyJoin = false;
      $bDisplayPositionField = false;
      //$bLogged = false;
      $bFilteredList = (bool)getValue('__filtered');

      //replay candoidate searches  (filters, sorting...)
      $nHistoryPk = (int)getValue('replay_search');
//BURADAN
      if($nHistoryPk > 0)
      {
        $this->csSearchId = getValue('searchId');
        //$asListMsg[] = 'replay search '.$nHistoryPk.': reload qb saved in db...';

        $asHistoryData = $oLogin->getUserActivityByPk($nHistoryPk);
        $poQB = $asHistoryData['data']['qb'];
        if(!$poQB || !is_object($poQB))
        {
          //dump($poQB);
          $poQB = $this->_getModel()->getQueryBuilder();
          $poQB->addWhere(' (false) ');
          $asListMsg[] = ' Error, could not reload the search. ';
        }
      }

      //Basic integration of the quick search tyhrough query builder
      if(!$poQB)
        $poQB = $this->_getModel()->getQueryBuilder();

      // ============================================
      // search and pagination management

      if(empty($this->csSearchId) && empty($nHistoryPk))
      {
        //$asListMsg[] = ' new search id [empty sId or history]. ';
        $this->csSearchId = manageSearchHistory($this->csUid, CONST_CANDIDATE_TYPE_CANDI);
        $poQB->addLimit('0, 50');
        $nLimit = 50;
      }
      else
      {
        //$asListMsg[] = ' just apply pager to reloaded search. ';
        $oPager = CDependency::getComponentByName('pager');
        $oPager->initPager();
        $nLimit = $oPager->getLimit();
        $nPagerOffset = $oPager->getOffset();

        $poQB->addLimit(($nPagerOffset*$nLimit).' ,'. $nLimit);
      }



      // =============================================================
      //TODO: to be moved when the search arrives

      $poQB->setTable('sl_candidate', 'scan');

      //join profile industry and occupation no matter what by default
      $poQB->addJoin('left', 'sl_candidate_profile', 'scpr', 'scpr.candidatefk = scan.sl_candidatepk');
      $poQB->addJoin('left', 'sl_company', 'scom', 'scom.sl_companypk = scpr.companyfk');
      $poQB->addJoin('left', 'sl_industry', 'sind', 'sind.sl_industrypk = scpr.industryfk');
      $poQB->addJoin('left', 'sl_occupation', 'socc', 'socc.sl_occupationpk = scpr.occupationfk');

      $sNow = date('Y-m-d H:i:s');
      $poQB->addSelect('scan.*,
          scom.name as company_name, scom.sl_companypk, scom.is_client as cp_client,
          (scpr.salary + scpr.bonus) as full_salary, scpr.grade, scpr.title, scpr._has_doc, scpr._in_play,
          scpr._pos_status, scpr.department, sind.label as industry, socc.label as occupation,
          TIMESTAMPDIFF(YEAR, scan.date_birth, "'.$sNow.'") AS age,
          scan.sl_candidatepk as PK');

      $poQB->addCountSelect('count(DISTINCT scan.sl_candidatepk) as nCount');


      $poQB->addJoin('left', 'event_link', 'elin', '(elin.cp_uid = "555-001" AND elin.cp_action = "ppav" AND elin.cp_type="candi" AND elin.cp_pk = scan.sl_candidatepk)');
      $poQB->addSelect('count(elin.eventfk) as nb_note');
      //$poQB->addSelect('MAX(elin.eventfk) as lastNote');
      $poQB->addSelect('MAX(elin.event_linkpk) as lastNote');

      if(!$oLogin->isAdmin())
      {
        $poQB->addWhere('(_sys_status = 0 OR _sys_redirect > 0)');
        $poQB->addSelect('IF(_sys_redirect > 0, _sys_redirect, scan.sl_candidatepk) as PK, 0 as _is_admin ');
      }
      else
        $poQB->addSelect(' 1 as _is_admin ');

      $sGroupBy = '';
      if(!empty($this->cnPk))
      {
        $asListMsg[] = ' + Mode name collect  ==> (status <= 3) ';
        $poQB->addWhere('scan.sl_candidatepk = '.$this->cnPk);
      }


      //-----------------------------------------------------------------------------
      //-----------------------------------------------------------------------------
      //add to the queryBuilder specific conditions for pipe or other custom filters

      $nFolderPk = getValue('folderpk');

      if(!empty($nFolderPk))
      {
        //$bLogged = $this->_addFolderFilter($asListMsg, $poQB);
        $nHistoryPk = $this->_addFolderFilter($asListMsg, $poQB);
      }


      if(getValue('pipe_filter'))
      {
        $this->_addPipeFilter($asListMsg, $poQB, $bDisplayPositionField);
      }

      if($sTemplate == 'name_collect' || 'display' == 'last notes' || 'dba' == 'tools')
      {
        $bHeavyJoin = true;

        if($sTemplate == 'name_collect')
        {
          $asListMsg[] = ' + Mode name collect  ==> (status <= 3) ';
          $poQB->addWhere('scan.statusfk <= 3');
        }
      }

      //-----------------------------------------------------------------------------
      //-----------------------------------------------------------------------------


      //manage default options
      if(!$poQB->hasLimit())
        $poQB->addLimit('0, 50');


      // -=- -=- -=- -=- -=- -=- -=- -=- -=- -=- -=- -=- -=- -=- -=- -=- -=-
      // manage sort field / order
      //no scan.sl_candidatepk  --> make the HeavyJoin mode crash (subQuery)
      $sSortField = getValue('sortfield'); // burasi

      if($sSortField == '_in_play')
      {
        $sSortOrder = getValue('sortorder', 'DESC');
        $poQB->addSelect('IF(_pos_status > 0 AND _pos_status < 101, (_pos_status+1000), IF(_pos_status = 151, 651, IF(_pos_status >= 150 AND _pos_status < 201, (_pos_status+100),  _pos_status))) as sort_status ');
        //$poQB->setOrder('_in_play '.$sSortOrder.', sort_status '.$sSortOrder.' ');
      }

      /*if(!empty($sSortField))
      {
        if($sSortField == '_in_play')
        {
          $sSortOrder = getValue('sortorder', 'DESC');
          $poQB->addSelect('IF(_pos_status > 0 AND _pos_status < 101, (_pos_status+1000), IF(_pos_status = 151, 651, IF(_pos_status >= 150 AND _pos_status < 201, (_pos_status+100),  _pos_status))) as sort_status ');
          $poQB->setOrder('_in_play '.$sSortOrder.', sort_status '.$sSortOrder.' ');
        }
        else
        {
          $sort_order = getValue('sortorder', 'DESC');

          if ($sSortField == 'salary')
            $sSortField = 'full_salary';
          else if ($sSortField == 'date_birth')
            $sSortField = 'age';

          $ordering = $sSortField.' '.$sort_order.$secondary_order;

          $poQB->setOrder($ordering);
        }
      }
      else*/
        $poQB->addOrder('scan.firstname DESC');


      if(empty($sGroupBy))
        $poQB->addGroup('scan.sl_candidatepk', false);
      else
        $poQB->addGroup($sGroupBy, false);


      $sMessage = $poQB->getTitle();
      if(!empty($sMessage))
        $asListMsg[] = $sMessage;

      // =====================================================================================

      //dump($poQB);
      $sQuery = $poQB->getCountSql();

      if(isset($exploded[1]) && !isset($exploded[2]) && $exploded[1] == "QuickSearch")
      {
        $searchID = $exploded[1];

        $savedQuery = getLoggedQuery($searchID);
        $sQuery = $savedQuery[0]['action'];

        $oDbResult = $oDb->ExecuteQuery($sQuery);
        $bRead = $oDbResult->readFirst();
        $all = $oDbResult->getAll();
        $nResult = count($all);
      }
      else
      {
        $oDbResult = $oDb->ExecuteQuery($sQuery);
        $bRead = $oDbResult->readFirst();
        $nResult = (int)$oDbResult->getFieldValue('nCount');
      }

      if(!$bRead || $nResult == 0)
      {
        $sDebug = '<a href="javascript:;" onclick="$(this).parent().find(\'.query\').toggle(); ">query... </a>
          <span class="hidden query"><br />'.$sQuery.'</span><br /><br /><br />';
        return $this->_oDisplay->getBlocMessage('No candidate found for: '.implode(', ', $asListMsg)).$sDebug;
      }

      //$nResult = (int)$oDbResult->getFieldValue('nCount');
      $sQuery = $poQB->getSql();
      //dump($sQuery);

      if ($nPagerOffset)
      {
        $record_start = $nPagerOffset*$nLimit;

        if ($record_start > $nResult)
        {
          $poQB->addLimit('0, '.$nLimit);
          $sQuery = $poQB->getSql();
          $oPager->setOffset(1);
        }
      }

      //Some joins are too heavy to make (notes, contacts...)
      //So we put the main query in a subquery, and join with the filtered / size-limited result
      if($bHeavyJoin)
      {
        if($sTemplate == 'name_collect')
        {
          $sQuery = 'SELECT *, GROUP_CONCAT(DISTINCT(scon.value) SEPARATOR ", ") as contact_detail FROM ('.$sQuery.') as candidate ';

          $sQuery.= ' LEFT JOIN sl_contact as scon ON (scon.item_type = \'candi\' AND scon.itemfk = candidate.sl_candidatepk) ';
          $sQuery.= ' WHERE candidate.statusfk <= 3 ';
          $sQuery.= ' GROUP BY candidate.sl_candidatepk ';
          //if($flag != false)
          //{
            $asSql = $poQB->getSqlArray(); // burasi
            if(!empty($asSql['order']))
              $sQuery.= ' ORDER BY '.implode(', ', $asSql['order']);
          //}

        }
      }
        $oldQ = $sQuery;
        $sQuery = explode("ORDER BY",$sQuery); // sacma sapan order by ekliyordi sildik


        $limit = $sQuery[1];
        $limit = explode("LIMIT", $limit);
        $limit = $limit[1];

        $sQuery = $sQuery[0];

        $sSortOrder = getValue('sortorder');


        if(!empty($sSortField) && !empty($sSortOrder) && $sSortField != null && $sSortOrder != null)
        {
          if($sSortField == "sl_candidatepk")
          {
            $sQuery.= ' ORDER BY scan.sl_candidatepk '.$sSortOrder." ";
          }
          else if($sSortField == "cp_client")
          {
            $sQuery.= ' ORDER BY scan.is_client '.$sSortOrder." ,cp_client ".$sSortOrder." ";
          }
          else if($sSortField == "_in_play")
          {
            //$sSortOrder = getValue('sortorder', 'DESC');
            //$poQB->addSelect('IF(_pos_status > 0 AND _pos_status < 101, (_pos_status+1000), IF(_pos_status = 151, 651, IF(_pos_status >= 150 AND _pos_status < 201, (_pos_status+100),  _pos_status))) as sort_status ');
            //$poQB->setOrder('_in_play '.$sSortOrder.', sort_status '.$sSortOrder.' ');
            $sQuery.= ' ORDER BY _in_play '.$sSortOrder." ,sort_status ".$sSortOrder.' ';
          }
          else if($sSortField == "grade")
          {
            $sQuery.= ' ORDER BY scpr.grade '.$sSortOrder." ";
          }
          else if($sSortField == "_has_doc")
          {
            $sQuery.= ' ORDER BY scpr._has_doc '.$sSortOrder." ";
          }
          else if($sSortField == "lastname")
          {
            $sQuery.= ' ORDER BY TRIM(scan.lastname) '.$sSortOrder." ";
          }
          else if($sSortField == "firstname")
          {
            $sQuery.= ' ORDER BY TRIM(scan.firstname) '.$sSortOrder." ";
          }
          else if($sSortField == "company_name")
          {
            $sQuery.= ' ORDER BY TRIM(scom.name) '.$sSortOrder." ";
          }
          else if($sSortField == "title")
          {
            $sQuery.= ' ORDER BY TRIM(scpr.title) '.$sSortOrder." ";
          }
          else if($sSortField == "department")
          {
            $sQuery.= ' ORDER BY TRIM(scpr.department) '.$sSortOrder." ";
          }
          else if($sSortField == "lastNote")
          {
            $sQuery.= ' ORDER BY lastNote '.$sSortOrder." ";
          }
          else if($sSortField == "date_birth")
          {
            $sQuery.= ' ORDER BY age '.$sSortOrder." ";
          }
          else if($sSortField == "salary")
          {
            $sQuery.= ' ORDER BY full_salary '.$sSortOrder." ";
          }
        }
        else if(strpos($oldQ,"ratio DESC, ratio_rev DESC") !== false)
        {
          $sQuery.= ' ORDER BY  IF(MAX(ratio_rev) >= MAX(ratio), ratio,ratio_rev) DESC , lastname desc, firstname desc, PK desc ';
          //$sQuery.= ' ORDER BY  IF(MAX(ratio) >= MAX(ratio_rev), ratio, ratio_rev) DESC ';
          //$sQuery.= ' IF(MAX(ratio_rev) >= MAX(ratio), ratio_rev, ratio) DESC ';
        }
        else if(strpos($sQuery,"ratio_rev") !== false)
        {
          $sQuery.= ' ORDER BY  IF(MAX(ratio) >= MAX(ratio_rev), ratio, ratio_rev) DESC , lastname desc, firstname desc, PK desc ';
        }
        else if(strpos($sQuery,"AS ratio") !== false)
        {
            $sQuery.= ' ORDER BY ratio DESC , lastname desc, firstname desc, PK desc ';
        }
        else
        {
          $sQuery.= ' ORDER BY TRIM(scan.lastname) ASC, TRIM(scan.firstname) ASC ';
        }

        if(!empty($limit))
          $sQuery.= " LIMIT ".$limit;
        else
        {
          $sQuery = explode('LIMIT', $sQuery);
          $sQuery = $sQuery[0];
          //$sQuery.= 'ORDER BY scan.firstname DESC';
        }

      $user_id = $oLogin->getUserPk();

      $limitlessQuery = explode('LIMIT', $sQuery);
      $limitlessQuery = $limitlessQuery[0];

      $searchTitle = explode(':',$poQB->getTitle());
      if(isset($searchTitle[1]))
      {
        $desc = $searchTitle[1];
      }
      else
      {
        $desc = "";
      }
      if(isset($searchTitle[0]))
      {
        $searchTitle = $searchTitle[0];
        if($searchTitle == "QuickSearch")
        {
          insertLog($user_id, '-1', $limitlessQuery,"quick_search",$desc);
        }
        else if($searchTitle == "CpxSearch")
        {
          insertLog($user_id, '-1', $limitlessQuery,"complex_search",$desc);
        }
        else // mainpage search links...
        {
          insertLog($user_id, '-1', $limitlessQuery,"other_search",$desc);
        }
      }

      /*if(getValue('pipe_filter')) // met icin tekrar yazacaktim ama dogru calisiyor gibi kontrol
      {
        $pipe_filter = getValue('pipe_filter');
        if($pipe_filter == "met")
        {
          //recently met sectigimizde buraya dusuyor
          $searchDateStart = strtotime ( '-6 month' , strtotime ( $sNow ) ) ;
          $searchDateStart = date ( 'Y-m-d H:i:s' , $searchDateStart );
          $recentlyMetQuery = "SELECT DISTINCT(slm.candidatefk) FROM sl_meeting slm WHERE slm.attendeefk = '".$user_id."'
          AND slm.meeting_done = '1' AND slm.date_meeting >= '".$searchDateStart."' ";

          $rmResultDB = $oDb->ExecuteQuery($recentlyMetQuery);
          $rmResult = $rmResultDB->getAll();
        }
      }*/
//ChromePhp::log($sQuery);
      $oDbResult = $oDb->ExecuteQuery($sQuery);
      $bRead = $oDbResult->readFirst();

      if(!$bRead || !isset($nResult))
      {
        assert('false; // count query returned results but not the select');
        return $this->_oDisplay->getBlocMessage('No candidate found.');
      }

      //------------------------------------------------------------------
      //------------------------------------------------------------------
      //Query done, we've got results,  we're about to generate the HTML results
      // we save the query just before.
      $_SESSION['555-001']['query'][$this->csSearchId] = $sQuery;

      //save search in history if it's a new search
      if(empty($nHistoryPk) /*&& !$bLogged*/)
      {
        $sURL = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_LIST, CONST_CANDIDATE_TYPE_CANDI, 0, array('searchId' => $this->csSearchId));
        $sLink = 'javascript: loadAjaxInNewTab(\''.$sURL.'\', \'candi\', \'candidate\');';
        $nHistoryPk = logUserHistory($this->csUid, $this->csAction, $this->csType, $this->cnPk, array('text' => implode(', ', $asListMsg).' (#'.$nResult.' results)', 'link' => $sLink, 'data' => array('qb' => $poQB)), false);
      }



// BURAYA KADAR

      $asData = array();
      $asPk = array();

      if(isset($exploded[1]) && !isset($exploded[2]) && $exploded[1] == "QuickSearch")
      {
        $searchID = $exploded[1];

        $savedQuery = getLoggedQuery($searchID);
        $sQuery = $savedQuery[0]['action'];

        $oDbResult = $oDb->ExecuteQuery($sQuery);
        $bRead = $oDbResult->readFirst();
        $all = $oDbResult->getAll();
        $nResult = count($all);
      }
      $oDbResult = $oDb->ExecuteQuery($sQuery);
      $bRead = $oDbResult->readFirst();

      while($bRead)
      {
        $asCandidate = $oDbResult->getData();
        $asCandidate['g'] = $asCandidate['lastname'].' '.$asCandidate['firstname'];
        $asCandidate['h'] = $asCandidate['company_name'];

        if(empty($asCandidate['created_by']))
          $asCandidate['k'] = '-';
        else
          $asCandidate['k'] = $oLogin->getUserLink((int)$asCandidate['created_by'], true);

        $asCandidate['n'] = $asCandidate['title'];

        if($bDisplayPositionField)
        {
          if($asCandidate['_in_play'] == 1)
          {
            $asCandidate['activity'] = '#'.$asCandidate['position_play'].' - '.$asCandidate['position_play_name'];
          }
          elseif($asCandidate['_in_play'] > 1)
          {
            $asCandidate['activity'] = $asCandidate['_in_play'].' positions: #'.$asCandidate['position_play_name'].'...';
          }
          else
          {
            if(!empty($asCandidate['position_play']))
            {
              $asCandidate['activity'] = '<em> positions: #'.$asCandidate['position_play_name'].'</em>';
            }
            else
              $asCandidate['activity'] = '';
          }
        }

        $asPk[] = (int)$asCandidate['sl_candidatepk'];
        $asData[(int)$asCandidate['sl_candidatepk']] = $asCandidate;



        $bRead = $oDbResult->readNext();
      }

      //Template related -- #1
      //params for the sub-templates when required
      switch($sTemplate)
      {
        case 'name_collect':
          $asParam = array('sub_template' => array('CTemplateList' => array(0 => array('row' => array('class' => 'CCandi_nc', 'path' => $_SERVER['DOCUMENT_ROOT'].self::getResourcePath().'template/candi_nc.tpl.class.php5')))));
          break;

        case 'pipeline':
          $asParam = array('sub_template' => array('CTemplateList' => array(0 => array('row' => array('class' => 'CCandi_nc', 'path' => $_SERVER['DOCUMENT_ROOT'].self::getResourcePath().'template/candi_pipeline.tpl.class.php5')))));
          break;

        default:

          $this->_addNoteData($asData, $asPk);
          $asParam = array('sub_template' => array('CTemplateList' => array(0 => array('row' => array('class' => 'CCandi_row', 'path' => $_SERVER['DOCUMENT_ROOT'].self::getResourcePath().'template/candi_row.tpl.class.php5')))));
          break;
      }

      //initialize the template
      $oTemplate = $this->_oDisplay->getTemplate('CTemplateList', $asParam);

      //if required, set specific params for the template
      $sListId = uniqid();
      $oTemplate->setTemplateParams('CTemplateList', array('id' => $sListId, 'data-type' => 'candi'));

      //get the config object for a specific template (contains default value so it works without config)
      $oConf = $oTemplate->getTemplateConfig('CTemplateList');

      $oConf->setRenderingOption('full', 'full', 'full');


      $sActionContainerId = uniqid();
      $sPic = $this->_oDisplay->getPicture(self::getResourcePath().'/pictures/list_action.png');
      $sJavascript = "var oCurrentLi = $(this).closest('li');

        if($('> div.list_action_container', oCurrentLi).length)
        {
          $('> div.list_action_container', oCurrentLi).fadeToggle();
        }
        else
        {
          var oAction = $('#".$sActionContainerId."').clone().show(0);

          $(oCurrentLi).append('<div class=\'list_action_container hidden\'></div><div class=\'floatHack\' />');
          $('div.list_action_container', oCurrentLi).append(oAction).fadeIn();
        }";

      //Template related -- #2
      if($nResult <= $nLimit)
      {
        $sSortJs = 'javascript';
        $sURL = '';
        $nAjax = 0;
      }
      else
      {
        $sSortJs = '-';
        $sURL = $this->_oPage->getAjaxUrl('sl_candidate', $this->csAction, CONST_CANDIDATE_TYPE_CANDI, 0, array('searchId' => $this->csSearchId, '__filtered' => 1, 'data_type' => CONST_CANDIDATE_TYPE_CANDI, 'replay_search' => $nHistoryPk));
        $nAjax = 1;
      }

      $sActionLink = $this->_oDisplay->getLink($sPic, 'javascript:;', array('onclick' => $sJavascript));
      $oConf->addColumn($sActionLink, 'a', array('id' => 'aaaaaa', 'width' => '20', 'class' => 'column_static_20'));
      $oConf->addColumn('ID', 'sl_candidatepk', array('id' => 'bbbbbb', 'width' => '43', 'style' => 'margin: 0;',
        'class' => 'column_static_43',
        'sortable'=> array($sSortJs => 'text', 'ajax' => $nAjax, 'url' => $sURL, 'ajax_target' => $this->csSearchId)));


      switch($sTemplate)
      {
        case 'name_collect':
          $oConf->addColumn('Lastname', 'lastname', array('id' => '', 'width' => '12%', 'sortable'=> array($sSortJs => 'text', 'ajax' => $nAjax, 'url' => $sURL, 'ajax_target' => $this->csSearchId)));
          $oConf->addColumn('Firstname', 'firstname', array('id' => '', 'width' => '12%', 'sortable'=> array($sSortJs => 'text', 'ajax' => $nAjax, 'url' => $sURL, 'ajax_target' => $this->csSearchId)));
          $oConf->addColumn('Company', 'h', array('id' => '', 'width' => '21%', 'sortable'=> array($sSortJs => 'text', 'ajax' => $nAjax, 'url' => $sURL, 'ajax_target' => $this->csSearchId)));
          $oConf->addColumn('Contact details', 'contact', array('id' => '', 'width' => '46%', 'sortable'=> array($sSortJs => 'text', 'ajax' => $nAjax, 'url' => $sURL, 'ajax_target' => $this->csSearchId)));
          break;

        default:

          //if we need to display play data, we shrink other columns
          if($bDisplayPositionField)
          {
            $sFistnameW = '10%'; $sLastnameW = '10%'; $sCompanyW = '14%';
            $sTitleW = '10%'; $sDeptW = '9%';

            $firstname_class = 'column_10'; $lastname_class = 'column_10'; $company_class = 'column_14';
            $title_class = 'column_10'; $dept_class = 'column_9';
          }
          else
          {
            $sFistnameW = '13%'; $sLastnameW = '13%'; $sCompanyW = '18%';
            $sTitleW = '11%'; $sDeptW = '10%';

            $firstname_class = 'column_13'; $lastname_class = 'column_13'; $company_class = 'column_18';
            $title_class = 'column_11'; $dept_class = 'column_10';
          }

          $oConf->addColumn('C', 'cp_client', array('id' => '', 'width' => '16', 'class' => 'column_static_16',
            'sortable'=> array($sSortJs => 'value_integer', 'ajax' => $nAjax, 'url' => $sURL, 'ajax_target' => $this->csSearchId)));

          $oConf->addColumn('Status', '_in_play', array('id' => '', 'width' => '40', 'class' => 'column_static_40',
            'sortable'=> array($sSortJs => 'value_integer', 'ajax' => $nAjax, 'url' => $sURL, 'ajax_target' => $this->csSearchId)));

          $oConf->addColumn('G', 'grade', array('id' => '', 'width' => '16', 'class' => 'column_static_16',
            'sortable'=> array($sSortJs => 'value_integer', 'ajax' => $nAjax, 'url' => $sURL, 'ajax_target' => $this->csSearchId)));

          $oConf->addColumn('R', '_has_doc', array('id' => '', 'width' => '16', 'class' => 'column_static_16',
            'sortable'=> array($sSortJs => 'value_integer', 'ajax' => $nAjax, 'url' => $sURL, 'ajax_target' => $this->csSearchId)));

          $oConf->addColumn('Lastname', 'lastname', array('id' => '', 'width' => $sFistnameW, 'class' => $firstname_class,
            'sortable'=> array($sSortJs => 'text', 'ajax' => $nAjax, 'url' => $sURL, 'ajax_target' => $this->csSearchId)));

          $oConf->addColumn('Firstname', 'firstname', array('id' => '', 'width' => $sLastnameW, 'class' => $lastname_class,
            'sortable'=> array($sSortJs => 'text', 'ajax' => $nAjax, 'url' => $sURL, 'ajax_target' => $this->csSearchId)));

          $oConf->addColumn('Company', 'company_name', array('id' => '', 'width' => $sCompanyW, 'class' => $company_class,
            'sortable'=> array($sSortJs => 'text', 'ajax' => $nAjax, 'url' => $sURL, 'ajax_target' => $this->csSearchId)));


          if($bDisplayPositionField)
          {
            $oConf->addColumn('In play at', 'position_play_company', array('id' => '', 'width' => '10%',
              'class' => 'column_10'));
            $oConf->addColumn('In play for', 'activity', array('id' => '', 'width' => '15%',
              'class' => 'column_15'));
          }
          else
          {
            //~150px
            if(in_array('title', $this->casSettings['candi_list_field']))
              $oConf->addColumn('Title', 'title', array('id' => '', 'width' => $sTitleW, 'class' => $title_class,
                'sortable'=> array($sSortJs => 'text', 'ajax' => $nAjax, 'url' => $sURL, 'ajax_target' => $this->csSearchId)));
          }

          if(in_array('department', $this->casSettings['candi_list_field']))
            $oConf->addColumn('Department', 'department', array('id' => '', 'width' => $sDeptW, 'class' => $dept_class,
              'sortable'=> array($sSortJs => 'text', 'ajax' => $nAjax, 'url' => $sURL, 'ajax_target' => $this->csSearchId)));

          if(in_array('note', $this->casSettings['candi_list_field']))
            $oConf->addColumn('Note', 'lastNote', array('id' => '', 'width' => '35', 'class' => 'column_static_35',
              'sortable'=> array($sSortJs => 'value_integer', 'ajax' => $nAjax, 'url' => $sURL, 'ajax_target' => $this->csSearchId)));

          if(in_array('date_birth', $this->casSettings['candi_list_field']))
            $oConf->addColumn('Age', 'date_birth', array('id' => '', 'width' => '30', 'class' => 'column_static_30',
              'sortable' => array($sSortJs => 'integer', 'ajax' => $nAjax, 'url' => $sURL, 'ajax_target' => $this->csSearchId)));

          if(in_array('salary', $this->casSettings['candi_list_field']))
            $oConf->addColumn('Salary', 'salary', array('id' => '', 'width' => '42', 'class' => 'column_static_42',
              'sortable'=> array($sSortJs => 'value_integer', 'ajax' => $nAjax, 'url' => $sURL, 'ajax_target' => $this->csSearchId)));

          if(in_array('manager', $this->casSettings['candi_list_field']))
            $oConf->addColumn('Managed by', 'manager', array('id' => '', 'width' => '105', 'class' => 'column_static_105',)); //108px

          break;
      }

      $oConf->addBlocMessage('<span class="search_result_title_nb">'.$nResult.' result(s)</span> '.implode(', ', $asListMsg), array(), 'title');

      //$sURL = $this->_oPage->getAjaxUrl('sl_candidate', CONST_ACTION_SEARCH, CONST_CANDIDATE_TYPE_CANDI, 0, array('searchId' => $this->csSearchId, '__filtered' => 1));
      $sURL = $this->_oPage->getAjaxUrl('sl_candidate', $this->csAction, CONST_CANDIDATE_TYPE_CANDI, 0, array('searchId' => $this->csSearchId, '__filtered' => 1, 'data_type' => CONST_CANDIDATE_TYPE_CANDI, 'replay_search' => $nHistoryPk));
      $oConf->setPagerTop(true, 'right', $nResult, $sURL.'&list=1', array('ajaxTarget' => '#'.$this->csSearchId));
      $oConf->setPagerBottom(true, 'right', $nResult, $sURL.'&list=1', array('ajaxTarget' => '#'.$this->csSearchId));

      //===========================================
      //===========================================
      //start building the HTML
      $sHTML = '';

      /* debug
       *
      if(!$bFilteredList)
        $sHTML.= $this->_oDisplay->getBlocStart($this->csSearchId, array('class' => 'scrollingContainer')).' new list';
      else
        $sHTML.= 'replay a search, pager offset '.$nPagerOffset.', container/search ID '.$this->csSearchId;*/

      if(!$bFilteredList)
        $sHTML.= $this->_oDisplay->getBlocStart($this->csSearchId, array('class' => 'scrollingContainer'));


        $sHTML.= $this->_oDisplay->getBlocStart($sActionContainerId, array('class' => 'hidden'));
        $sHTML.= '
          <div><input type="checkbox"
          onchange="if($(this).is(\':checked\')){ listSelectBox(\''.$sListId.'\', true); }else{ listSelectBox(\''.$sListId.'\', false); }"/>Select all</div>';

        $sURL = $this->_oPage->getAjaxUrl('sl_folder', CONST_ACTION_ADD, CONST_FOLDER_TYPE_FOLDER, 0, array('item_type' => CONST_CANDIDATE_TYPE_CANDI));
        $sHTML.= '<div>Create a folder from [<a href="javascript:;" onclick="
          listBoxClicked($(\'#'.$sListId.' ul li:first\'));
          sIds = $(\'.multi_drag\').attr(\'data-ids\');
          if(!sIds)
            return alert(\'Nothing selected\');

          goPopup.setLayerFromAjax(\'\', \''.$sURL.'&ids=\'+sIds);">selected items</a>] OR';

        if($nResult <= 80000)
          $sHTML.= ' [<a href="javascript:;" onclick="goPopup.setLayerFromAjax(\'\', \''.$sURL.'&searchId='.$this->csSearchId.'\');">All '.$nResult.' results</a>]';
        else
          $sHTML.= ' [<span title="Too many results. Can\'t save more than 50000 results." style="font-style: italic">all</span> ]';

        $sURL = $this->_oPage->getAjaxUrl('sl_folder', CONST_ACTION_ADD, CONST_FOLDER_TYPE_ITEM, 0, array('item_type' => CONST_CANDIDATE_TYPE_CANDI));
        $sHTML.= '</div><div>Move into a folder [<a href="javascript:;" onclick="
          listBoxClicked($(\'#'.$sListId.' ul li:first\'));
          sIds = $(\'.multi_drag\').attr(\'data-ids\');
          if(!sIds)
            return alert(\'Nothing selected\');

          goPopup.setLayerFromAjax(\'\', \''.$sURL.'&ids=\'+sIds);">selected items</a>] OR';

        if($nResult <= 80000)
          $sHTML.= ' [<a href="javascript:;" onclick="goPopup.setLayerFromAjax(\'\', \''.$sURL.'&searchId='.$this->csSearchId.'\');">All '.$nResult.' results</a>]';
        else
          $sHTML.= ' [<span title="Too many results. Can\'t save more than 50000 results." style="font-style: italic">all</span> ]';

        $sHTML.= '</div>';

        if ($nResult > 1 && empty($nFolderPk))
        {
          $sURL = $this->_oPage->getAjaxUrl('settings', CONST_ACTION_SAVEEDIT, CONST_TYPE_SAVED_SEARCHES, 0,
            array('action' => 'add', 'activity_id' => $nHistoryPk));

          $sHTML.= '<div><a href="javascript:;" onclick="ajaxLayer(\''.$sURL.'\', 370, 150);">Save this search</a></div>';
        }

        if(!empty($nFolderPk))
        {
          $folder_obj = CDependency::getComponentByName('sl_folder');
          $folder_db = $folder_obj->getFolder((int)$nFolderPk);

          $read = $folder_db->readFirst();

          $folder_owner = $folder_db->getFieldValue('ownerloginfk');
          $current_user = $this->_oLogin->getUserPk();

          if ($folder_owner == $current_user || $this->_oLogin->isAdmin())
          {
            $sURL = $this->_oPage->getAjaxUrl('sl_folder', CONST_ACTION_DELETE, CONST_FOLDER_TYPE_ITEM,
              0, array('folderpk' => $nFolderPk, 'item_type' => CONST_CANDIDATE_TYPE_CANDI));
            $sHTML.= '<div>Remove from folder [<a href="javascript:;" onclick="listBoxClicked($(\'#'.$sListId.' ul li:first\'));
            sIds = $(\'.multi_drag\').attr(\'data-ids\');
            if(!sIds)
              return alert(\'Nothing selected\');

             AjaxRequest(\''.$sURL.'&ids=\'+sIds);
            ">selected</a>]
            [<a href="javascript:;" onclick="AjaxRequest(\''.$sURL.'&searchId='.$this->csSearchId.'\');">'.$nResult.' results</a>]</div>';
          }
        }

        $sHTML.= $this->_oDisplay->getBlocEnd();

        $test_value = getValue('pipe_filter');

        if(isset($test_value) && $test_value == "placed")
        {
          // when add new candidate foreach does not work...
          foreach($asData as $key => $value) // MCA pipe_filter placed ise tum adaylarin statusunu placed yaptik
          {
              $asData[$key]['_pos_status'] = 101;
          }
        }

        //Add the list template to the html
        $sHTML.= $oTemplate->getDisplay($asData, 1, 5, 'safdassda');


        //---------------------------------------------
        //manage javascript action
        $sURL = $this->_oPage->getAjaxUrl('sl_folder', CONST_ACTION_SAVEADD, CONST_FOLDER_TYPE_ITEM, 0);
        $sHTML.='<script> initDragAndDrop(\''.$sURL.'\'); </script>';

        if(count($asData) == 1)
        {
          $asData = current($asData);
          $sURL = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, (int)$asData['sl_candidatepk']);
          $sHTML.='<script> view_candi(\''.$sURL.'\'); </script>';
        }

        //DEBUG: Dropp the query at the end
        if($oLogin->getUserPk() == 367 || isDevelopment() )
        {
          $sHTML.= '<a href="javascript:;" onclick="$(this).parent().find(\'.query\').toggle(); ">query... </a>
            <span class="hidden query"><br />'.$sQuery.'</span><br /><br /><br />';
        }

        $sHTML .= '<script>
          $(function(){
            var list_container = document.getElementById(\''.$this->csSearchId.'\');
            $(\'.fixedListheader\').remove();
            list_container.scrollTop = 0;
          });
        </script>';

        if($gbNewSearch)
          $sHTML.= $this->_oDisplay->getBlocEnd();

      return $sHTML;
    }


    private function _addFolderFilter(&$asListMsg, &$poQB)
    {

      $nFolderPk = (int)getValue('folderpk');

      if(!isset($nFolderPk) || $nFolderPk == '' || $nFolderPk == 0)
      {
        $asListMsg[] = $this->_oDisplay->getBlocMessage('Please select consultant again.');
        return 0;
      }

      $oFolder = CDependency::getComponentByName('sl_folder');
      $oDbFolder = $oFolder->getFolder($nFolderPk);
      $bRead = $oDbFolder->readFirst();
      if(!$bRead)
      {
        $asListMsg[] = $this->_oDisplay->getBlocMessage('Folder not found. It may have been deleted');
        return 0;
      }

      $sFolderName = $oDbFolder->getFieldValue('label');
      $asFolderItem = $oFolder->getFolderItem($nFolderPk, true);
      if(empty($asFolderItem))
        $asFolderItem = array(0);

      $poQB->addSelect($nFolderPk.' as folderfk');
      $poQB->addWhere('scan.sl_candidatepk IN ('.implode(',', $asFolderItem).') ');
      $asListMsg[] = 'folder #'.$nFolderPk.' - '.$sFolderName;

      $sURL = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_LIST, CONST_CANDIDATE_TYPE_CANDI, 0, array('folderpk' => $nFolderPk));
      $sLink = 'javascript: loadAjaxInNewTab(\''.$sURL.'\', \'candi\', \'Folder \');';

      return logUserHistory('555-002', CONST_ACTION_VIEW, CONST_FOLDER_TYPE_FOLDER, $nFolderPk, array('text' => 'folder #'.$nFolderPk.':  '.$sFolderName, 'link' => $sLink, 'data' => array('qb' => $poQB)), false);
    }

    private function _addPipeFilter(&$asListMsg, &$poQB, &$pbPosField)
    {
      $sFilter = getValue('pipe_filter');

      $oLogin = CDependency::getCpLogin();
      $nCurrentUser = $oLogin->getUserPk();
      $nLoginfk = (int)getValue('pipe_user', 0);
      if(empty($nLoginfk))
        $nLoginfk = $nCurrentUser;

      if($nCurrentUser == $nLoginfk)
        $sBy = 'My ';
      else
        $sBy = $oLogin->getUserName($nLoginfk, true).'\'s ';

      $asStatus = array('in_play' => '< 150', 'pitched' => '= 1', 'resume_sent' => '= 2', 'stalled' => '= 150', 'fallen_off' => '= 200', 'placed' => '= 101');

      switch($sFilter)
      {
        case 'in_play':

          $asListMsg[] = $sBy.' [ in_play ] candidates';
          $poQB->addJoin('inner', 'sl_position_link', 'spli', 'spli.candidatefk = scan.sl_candidatepk AND spli.active = 1 AND spli.status > 0 AND spli.status < 101  AND spli.created_by = '.$nLoginfk.'');
          $poQB->addWhere('(scpr._in_play > 0 AND spli.created_by = '.$nLoginfk.')');
          $pbPosField = true;
          break;

        case 'pitched':
        case 'resume_sent':

          $asListMsg[] = $sBy.' [ '.str_replace('_', ' ', $sFilter).' ] candidates';
          $poQB->addJoin('inner', 'sl_position_link', 'spli', 'spli.candidatefk = scan.sl_candidatepk AND spli.active = 1 AND spli.status '.$asStatus[$sFilter].' AND spli.created_by = '.$nLoginfk.'');
          $pbPosField = true;
          break;

        case 'placed':

          $asListMsg[] = $sBy.' [ '.str_replace('_', ' ', $sFilter).' ] candidates';
          $poQB->addJoin('inner', 'sl_position_link', 'spli', 'spli.candidatefk = scan.sl_candidatepk AND spli.status '.$asStatus[$sFilter].' AND spli.created_by = '.$nLoginfk.'');
          $pbPosField = true;
          break;

        case 'ccm':

          $asListMsg[] = $sBy.' [ CCM ] candidates ';
          $poQB->addJoin('inner', 'sl_position_link', 'spli', 'spli.candidatefk = scan.sl_candidatepk AND spli.active = 1 AND spli.status > 50 AND spli.status < 100 AND spli.created_by = '.$nLoginfk.'');
          $pbPosField = true;
          break;

        case 'fallen_off':

          $asListMsg[] = $sBy.' [ fallen - not interested ] candidates ';
          $poQB->addJoin('inner', 'sl_position_link', 'spli', 'spli.candidatefk = scan.sl_candidatepk AND spli.active = 1 AND spli.status IN (200,201) AND spli.created_by = '.$nLoginfk.'');
          $pbPosField = true;
          break;

        case 'expired':

          $asListMsg[] = $sBy.' [ stalled - expired ] candidates ';
          $poQB->addJoin('inner', 'sl_position_link', 'spli', 'spli.candidatefk = scan.sl_candidatepk AND spli.active = 1 AND spli.status IN (150,151) AND spli.created_by = '.$nLoginfk.'');
          $pbPosField = true;
          break;

        case 'met':
        case 'met6':
        case 'met12':

          $nMonth = (int)str_replace('met', '', $sFilter);
          if(empty($nMonth)) //$nMonth = 3; // mitch asked to be 6 months
            $nMonth = 6;

          $sDate = date('Y-m-d', strtotime('-'.$nMonth.' month'));
          $asListMsg[] = $sBy.' Recently met candidates ('.$nMonth.' months || since'.$sDate.')';

          $dateNow = date('Y-m-j');
          $searchDateStart = strtotime ( '-6 month' , strtotime ( $dateNow ) ) ; // -3 month tu -6 yaptik
          $searchDateStart = date ( 'Y-m-j' , $searchDateStart );

          $poQB->addJoin('inner', 'sl_meeting', 'smee', 'smee.candidatefk = scan.sl_candidatepk AND smee.meeting_done = 1 AND smee.attendeefk = '.$nLoginfk.' AND smee.date_met >= "'.$searchDateStart.'"');// $sDate vardi $searchDateStart yaptik MCA

          break;

        case 'offer':
          $asListMsg[] = $sBy.' [ Offer ] candidates ';
          $poQB->addJoin('inner', 'sl_position_link', 'spli', 'spli.candidatefk = scan.sl_candidatepk AND spli.active = 1 AND spli.status = 100 AND spli.created_by = '.$nLoginfk.'');
          $pbPosField = true;
          break;

        case 'meeting':
          $sDate = date('Y-m-d', strtotime('+3 month'));
          $asListMsg[] = $sBy.' Scheduled meetings  (next 3 month )';

          $poQB->addJoin('inner', 'sl_meeting', 'smee', 'smee.candidatefk = scan.sl_candidatepk AND smee.meeting_done = 0 AND smee.attendeefk = '.$nLoginfk.' AND smee.date_meeting < "'.$sDate.'"');
          break;

        case 'rm':
          $asListMsg[] = $sBy.' Followed candidates [RM]';
          $poQB->addJoin('inner', 'sl_candidate_rm', 'scrm', 'scrm.candidatefk = scan.sl_candidatepk AND scrm.date_expired IS NULL AND scrm.loginfk = '.$nLoginfk);
          break;

        case 'all_active':
          $oLogin = CDependency::getCpLogin();
          if($nLoginfk == $oLogin->getUserPk())
            $asListMsg[] = 'Active candidates created by me';
          else
            $asListMsg[] = 'Active candidates created by '.$oLogin->getUserName($nLoginfk);

          $poQB->addWhere("( spli.in_play = '1' AND  ( spli.created_by = '".$nLoginfk."' OR ( scan.created_by = '".$nLoginfk."' AND ( scan.statusfk = '1' or scan.statusfk = '5' or scan.statusfk = '6' ) ) ) ) ");
          $poQB->addJoin('inner', 'sl_position_link', 'spli', 'spli.candidatefk = scan.sl_candidatepk AND spli.active = 1 AND spli.status <= 100 ');
          $pbPosField = true;
          break;

        case 'all':
        default:
          $oLogin = CDependency::getCpLogin();
          if($nLoginfk == $oLogin->getUserPk())
            $asListMsg[] = 'All Candidates created by me';
          else
            $asListMsg[] = 'All Candidates created by '.$oLogin->getUserName($nLoginfk);

          $poQB->addWhere('(scan.created_by = '.$nLoginfk.' OR scpr.managerfk = '.$nLoginfk.')');
          break;

      }

      if($pbPosField)
      {
        $poQB->addSelect('spli.positionfk as position_play, spli.created_by as playing_for,
          spd.title as position_play_name, scom_2.name as position_play_company ');

        $poQB->addJoin('left', 'sl_position_detail', 'spd', 'spd.positionfk = spli.positionfk');
        $poQB->addJoin('left', 'sl_position', 'spos', 'spos.sl_positionpk = spli.positionfk');
        $poQB->addJoin('left', 'sl_company', 'scom_2', 'scom_2.sl_companypk = spos.companyfk');
      }

      return true;
    }


    private function _getRandomText($pnMinSize = 1, $pnMaxSize = 12)
    {
      $nSize = rand($pnMinSize, $pnMaxSize);
      $sString = '';

      for($nCount = 0; $nCount < $nSize; $nCount++)
      {
        $sString .= chr(rand(97, 122));
      }

      return $sString;
    }

    private function _addNoteData(&$asData, $panPk)
    {
      $oNote = CDependency::getComponentByName('sl_event');
      //$oDbResult = $oNote->getLastEvent($panPk, '555-001', 'ppav', 'candi');
      //dump($oDbResult);

      //$bRead = $oDbResult->readFirst();
      //if(!$bRead)
        //return true;

      //while($bRead)
      foreach ($panPk as $key => $value)
      {
        $oDbResult = $oNote->getLastEvent($value, '555-001', 'ppav', 'candi');

        $noteArray = $oDbResult->getAll();

        if(!isset($noteArray[0]))
        {
          return true;
        }
        else
        {
          if(isset($noteArray[0]['title']))
          {
            $sContent = $noteArray[0]['title'].'<br />'.$noteArray[0]['content'];
          }
          else
          {
            $sContent = $noteArray[0]['content'];
          }
        }

        /*if($oDbResult->getFieldValue('title'))
          $sContent = $oDbResult->getFieldValue('title').'<br />'.$oDbResult->getFieldValue('content');
        else
          $sContent = $oDbResult->getFieldValue('content');*/

        //$nCandidatePk = (int)$oDbResult->getFieldValue('cp_pk');
        $nCandidatePk = (int)$noteArray[0]['cp_pk'];

        $asData[$nCandidatePk]['note_type'] = $noteArray[0]['type'];
        $asData[$nCandidatePk]['note_title'] = $noteArray[0]['title'];
        $asData[$nCandidatePk]['note_content'] = $noteArray[0]['content'];
        $asData[$nCandidatePk]['note_date'] = $noteArray[0]['date_create'];
      }
      return true;
    }



    private function _updateCompanyRss($pnCompanyPk = 0)
    {
      if(!assert('is_integer($pnCompanyPk)'))
        return false;

      //echo 'SL_Candidate cron >> _updateCompanyRss() <br />';

      //different behaviour if cron job or manually launched by user
      if(empty($pnCompanyPk))
      {
        //define how many to treat in this batch.
        // refresh all cp in 3 months with 68 batch a day (90 * 68) = 6120
        // every 15 minutes at night, every 30min during daytime
        $nCompany = $this->_getModel()->countCompanies();
        $nLimit =  ceil($nCompany / 6120) ;

        $sLimit = '1, '.$nLimit;
        $nSleep = 500000;
        $bManual = false;
        $sQuery = 'SELECT scom.*, scrs.date_created as dateRss
          FROM sl_company as scom
          LEFT JOIN sl_company_rss as scrs ON (scrs.companyfk = scom.sl_companypk)
          WHERE  LENGTH(scom.name) > 1
          ORDER BY scrs.date_created, sl_companypk DESC
          LIMIT '. $sLimit;

        //echo $sQuery.'<br />';
        $oDbResult = $this->_getModel()->executeQuery($sQuery);
      }
      else
      {
        $sWhere = 'sl_companypk = '.$pnCompanyPk;
        $sLimit = '';
        $nSleep = 10;
        $bManual = true;

        $oDbResult = $this->_getModel()->getByWhere('sl_company', $sWhere, '*', ' LENGTH(name) > 1 AND sl_companypk DESC', $sLimit);
      }


      $bRead = $oDbResult->readFirst();
      $nCount = 0;
      while($bRead)
      {
        $this->_updateCompanyFeed($oDbResult->getData(), $bManual);
        usleep($nSleep);

        $nCount++;
        $bRead = $oDbResult->readNext();
      }

      //echo '<br />'.$nCount.' company RSS updated';
      return true;
    }

    private function _updateCompanyFeed($pasCompanyData, $pbManual = false, $pnAttempt = 0)
    {
      if(!assert('is_array($pasCompanyData) && !empty($pasCompanyData)'))
        return false;

      if(!isset($pasCompanyData['sl_companypk']) || empty($pasCompanyData['sl_companypk']))
      {
        assert('false; // Missing company data in the rss feed');
        dump($pasCompanyData);
        return false;
      }
      if(empty($pasCompanyData['name']))
      {
        //there are some sadly, we do nothing and log it as done
         assert('false; // Company without name #'.$pasCompanyData['sl_companypk'].' !!!! ');
         dump($pasCompanyData);
         return false;
      }


      //If launched manually, we try an accurate search.
      //If no result, the function will be launched a second time
      //can't use as_epq= anymore, google is getting rid of RSS feeds
      /*if($pbManual && $pnAttempt == 0)
        $sNewsUrl = 'http://news.google.com/news/search?output=rss&gl=jp&geo=jp&q='.$pasCompanyData['name'];
      else*/
        $sNewsUrl = 'https://news.google.com/news/feeds?pz=1&cf=all&&output=rss&q='.urlencode($pasCompanyData['name']);

      try
      {
        libxml_use_internal_errors(true);
        $oXml = @new SimpleXMLElement($sNewsUrl, null, true);
        if(!$oXml)
        {
          throw new Exception('bad xml');
        }
      }
      catch(Exception $oEx)
      {
        assert('false; // could not load news feed from '.$sNewsUrl.'. Error: '.$oEx->getMessage());
        return false;
      }


      $asInsert = array();
      $asInsert['sl_company_rssfk'] = null;
      $asInsert['companyfk'] = (int)$pasCompanyData['sl_companypk'];
      $asInsert['date_created'] = date('Y-m-d H:i:s');
      $asInsert['url'] = $sNewsUrl;
      $asInsert['nb_news'] = 0;
      $asInsert['content'] = '';
      $sNews = '';

      $oChannel = $oXml->channel;
      if($oChannel)
      {
        //dump($oChannel);
        //count items (-10 for title, global desc, date, generator, image...)
        $asInsert['nb_news'] = count($oChannel->children()) - 10;

        $nCount = 0;
        foreach($oChannel->item as $oItem)
        {
          $sContent = (string)$oItem->description;
          $sEncoding = mb_detect_encoding(strip_tags($sContent));
          if($sEncoding == 'ASCII')
            $sEncoding = 'utf-8';

          $sContent = html_entity_decode($sContent, ENT_QUOTES, $sEncoding);
          $sContent = str_ireplace('<a ', '<a target="_blank" ', $sContent);
          $sContent = str_ireplace('<b>', '<b class="rss"> ', $sContent);
          //dump($sContent);

          $asMatch = array();
          preg_match_all('/<td.*>(.*)<\/td>/Ui', $sContent, $asMatch);

          $sContent = '<div>';
          $bFirst = true;
          foreach($asMatch[0] as $nKey => $sTd)
          {
            if($bFirst)
            {
              //dump('checking first TD: '.$sTd);
              $bFirst = false;
              $bHasPicture = false;
              $nPosition = stripos($sTd, 'img');
              //dump($nPosition);
              if($nPosition !== false)
              {
                //dump('found an <img in @ position '.$nPosition.' : '.$sTd);
                $sContent.= '<div class="feed_image">'.$asMatch[1][$nKey].'</div>';
                $bHasPicture = true;
              }
            }
            else
            {
              if($bHasPicture)
                $sContent.= '<div class="feed_content hasPicture">'.$asMatch[1][$nKey].'</div>';
              else
                $sContent.= '<div class="feed_content">'.$asMatch[1][$nKey].'</div>';
              //dump('Second TD: <div class="feed_content">'.$asMatch[1][$nKey].'</div>');
            }
          }
          $sContent.= '</div>';
          //dump('result for this item'.$sContent);

          $sNews.= '<div class="rss_news_container">';
          $sNews.= '<div class="rss_news_title">'.(string)$oItem->title.'</div>';
          $sNews.= '<div class="rss_news_source">Google news</div>';
          $sNews.= '<div class="rss_news_date">'.date('Y-m-d H:i:s', strtotime((string)$oItem->pubDate)).'</div>';
          $sNews.= '<div class="rss_news_content">'.$sContent.'</div>';
          $sNews.= '<div class="floatHack"></div>';
          $sNews.= '</div>';

          $nCount++;
          if($nCount >=3)
            break;
        }
      }

      //if the accurate search didn't work, I try to wider the scope
      if($pbManual && $pnAttempt == 0 && $nCount == 0)
      {
        return $this->_updateCompanyFeed($pasCompanyData, true, 1);
      }

      $dom_class = new DOMDocument();
      $dom_class->loadHTML($sNews);
      $fixed_html = $dom_class->saveHTML();

      $asInsert['content'] = $fixed_html;

      $this->_getModel()->deleteByFk($asInsert['companyfk'], 'sl_company_rss', 'companyfk');
      $nPk = $this->_getModel()->add($asInsert, 'sl_company_rss');

      if(!$nPk)
      {
        assert('false; // could not save the company feed '.var_export($asInsert, true));
        return false;
      }

     //echo 'RSS updated for company: '.$asInsert['companyfk'].'<hr />';
      return true;
    }





    // ====================================================================================
    // ====================================================================================
    // Start MEETING section


    private function _getCandidateMeetingHistory($pnCandiPk)
    {
      if(!assert('is_integer($pnCandiPk)'))
        return 'No history available';

      $oDbResult = $this->_getModel()->getByFk($pnCandiPk, 'sl_meeting', 'candidate', '*, IF(meeting_done = 1, 1, 0) as m_done', 'm_done, date_meeting');
      $bRead = $oDbResult->readFirst();

      $sHTML = '';
      $sHTML.= $this->_oDisplay->getBlocStart();

      //add a link to create meeting on the top right
      $sUrl = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_ADD, CONST_CANDIDATE_TYPE_MEETING, $pnCandiPk);


      $sLink = $this->_oDisplay->getLink('Set a new meeting', 'javascript:;', array('onclick' => 'goPopup.removeActive(); var oConf = goPopup.getConfig(); oConf.width = 950; oConf.height = 550; goPopup.setLayerFromAjax(oConf, \''.$sUrl.'\');'));
      $sHTML.= $this->_oDisplay->getBloc('', $sLink, array('style' => 'float: right; padding: 3px 5px; margin-bottom: 5px; background-color: #f0f0f0; border: 1px solid #ddd;'));
      $sHTML.= $this->_oDisplay->getFloatHack();

      if(!$bRead)
      {
        $sHTML = $this->_oDisplay->getTitle('Meeting history', 'h3', true);

        $sHTML.= $this->_oDisplay->getBlocStart('', array('style' => 'text-align: center; width: 400px; margin: 15px auto; padding: 15px; border: 1px solid #dedede;'));
        $sHTML.= '<em>No meeting set with this candidate.</em><br /><br />';

          $sHTML.= $this->_oDisplay->getBloc('', $sLink, array('style' => 'margin: 0 auto; width: 150px; text-align: center; background-color: #f0f0f0; border: 1px solid #ddd;'));

        $sHTML.= $this->_oDisplay->getBlocEnd();
      }
      else
      {
        $oLogin = CDependency::getCpLogin();
        $oRight = CDependency::getComponentByName('right');

        $nCurrentUser = $oLogin->getUserPk();
        $sNow = date('Y-m-d H:i:s');
        $sToday = date('Y-m-d');
        $sAweekAgo = date('Y-m-d H:i:s', strtotime('-1 week'));
        $bManager = $oRight->canAccess($this->csUid, CONST_ACTION_MANAGE, CONST_CANDIDATE_TYPE_MEETING);
        $asMeeting = array('active' => array(), 'inactive' => array());

        while($bRead)
        {
          $nMeetingPk = (int)$oDbResult->getFieldValue('sl_meetingpk');
          $sMeetingDate = $oDbResult->getFieldValue('date_meeting');
          $asDate = explode(' ', $sMeetingDate);
          $nAttendee = (int)$oDbResult->getFieldValue('attendeefk');
          $asButtons = array();
          $meetingDoneFlag = false;
          $meetingInfo = array();

/*          if($nCurrentUser == $nAttendee)
            $sLink = '- me -';
          else
            $sLink = $oLogin->getUserLink($nAttendee, true);
*/
//          $sLink = $oLogin->getUserLink($nAttendee, true);
          if($nAttendee == -1)
          {
            $sLink = "Unknown";
          }
          else
          {
            $user_info = getUserInformaiton($nAttendee);
            $sLink = $user_info['position']. ' '.$user_info['firstname']. ' '.$user_info['lastname'].' ';
          }
// DENEDIK
          $nStatus = (int)$oDbResult->getFieldValue('meeting_done');
          if($nStatus != 0)
          {
            $sType = 'inactive';

            if($nStatus < 0)
            {
              $sClass = 'meeting_cancelled';
              $sStatus = $this->_oDisplay->getText('cancelled', array('class' => $sClass));
            }
            else
            {
              $meetingDoneFlag = true;
              $sClass = 'meeting_done';
              $meetingInfo = getMeetingInformation((int)$oDbResult->getFieldValue('sl_meetingpk'));
              $meeting_type = $meetingInfo['type']; // 1:in person 2:by phone 3:video 4:other

              if($meeting_type == 1)
              {
                $meeting_type = "Met in person";
              }
              else if($meeting_type == 2)
              {
                $meeting_type = "Met by phone";
              }
              else if($meeting_type == 3)
              {
                $meeting_type = "Met by video chat";
              }
              else
              {
                $meeting_type = "Meeting done";
              }
              //$sStatus = $this->_oDisplay->getText('meeting done', array('class' => $sClass));
              $sStatus = $this->_oDisplay->getText($meeting_type, array('class' => $sClass));
            }
          }
          else
          {
            $sType = 'active';
            $sStatus = $this->_oDisplay->getText(' - need update -', array('class' => 'meeting_passed'));

            if($sMeetingDate < $sAweekAgo)
              $sClass = 'meeting_passed_late';
            elseif($sMeetingDate < $sNow)
              $sClass = 'meeting_passed';
            elseif(substr($sMeetingDate, 0, 10) == $sToday)
            {
              $sClass = 'meeting_close';
              $sStatus = $this->_oDisplay->getText('soon', array('class' => $sClass));
            }
            else
            {
              $sClass = '';
              //$sStatus = $this->_oDisplay->getText(' scheduled');
              $sStatus = $this->_oDisplay->getText('- need update -');
            }
          }
//DENEDIK

          $sMeeting = $this->_oDisplay->getBlocStart('', array('class' => 'meeting_row '.$sClass));

            $sMeeting.= $this->_oDisplay->getBloc('', 'Meeting set for ', array('style' => 'width:150px;','class' => 'meeting_row_forth'));
            $sMeeting.= $this->_oDisplay->getBloc('', $sLink, array('style' => 'width:auto;','class' => 'meeting_row_attendee'));


          $createdByFk = (int)$oDbResult->getFieldValue('created_by');

          if($createdByFk == -1)
          {
            $sLink = "Unknown";
          }
          else
          {
            $user_info = getUserInformaiton($createdByFk);
            $sLink = $user_info['position']. ' '.$user_info['firstname']. ' '.$user_info['lastname'].' ';
          }


            $sMeeting.= $this->_oDisplay->getBloc('', ' by', array('style' => 'width:20px; margin-left:5px;','class' => 'meeting_row_first'));
            $sMeeting.= $this->_oDisplay->getBloc('', $sLink, array('style' => 'width:auto;','class' => 'meeting_row_creator'));

            //$sMeeting.= $this->_oDisplay->getBloc('', 'on the <span>'.$asDate[0].'</span> at <span>'.substr($asDate[1], 0, 5).'</span> ', array('class' => 'meeting_row_date '.$sClass));
            $sMeeting.= $this->_oDisplay->getFloathack();


            //----------------------------------------------------
            //second row
            $date_created_flag = $oDbResult->getFieldValue('date_created');
            if(isset($date_created_flag))
              $asDateMeetingCreate = explode(' ',$oDbResult->getFieldValue('date_created')); // bu olusturulma saati o nedenle almadik MCA
/*            if($nCurrentUser == $oDbResult->getFieldValue('created_by'))
              $sLink = '- me -';
            else
              $sLink = $oLogin->getUserLink((int)$oDbResult->getFieldValue('created_by'), true);
*/

            if(isset($meetingInfo['date_created']))
            {
              $setDate = explode(' ',$meetingInfo['date_created']);

              if(isset($setDate) && isset($setDate[0]) && !empty($setDate[0]) && $setDate[0] != "")
              {
                $sMeeting.= $this->_oDisplay->getBloc('', 'Meeting scheduled on', array('style' => 'width:140px;', 'class' => 'meeting_row_first'));
                $sMeeting.= $this->_oDisplay->getBloc('', '<span>'.$setDate[0].'</span> at <span>'.substr($setDate[1], 0, 5).'</span> ', array('class' => 'meeting_row_date '.$sClass));
              }
            }

            if(isset($asDate) && isset($asDate[1]) && !empty($asDate[1]) && $asDate[1] != "")
            {
              //$sMeeting.= $this->_oDisplay->getBloc('', 'Meeting set by', array('class' => 'meeting_row_first'));
              $sMeeting.= $this->_oDisplay->getBloc('', 'Meeting scheduled for', array('style' => 'width:140px;', 'class' => 'meeting_row_first'));
              //$sMeeting.= $this->_oDisplay->getBloc('', $sLink, array('class' => 'meeting_row_creator'));
              //$sMeeting.= $this->_oDisplay->getBloc('', 'on the <span>'.$asDate[0].'</span>', array('class' => 'meeting_row_date'));
              $sMeeting.= $this->_oDisplay->getBloc('', '<span>'.$asDate[0].'</span> at <span>'.substr($asDate[1], 0, 5).'</span> ', array('class' => 'meeting_row_date '.$sClass));
            }
            $sMeeting.= $this->_oDisplay->getFloathack();

            //----------------------------------------------------
            //Third row
            $sMeeting.= $this->_oDisplay->getBloc('', 'Status', array('style' => 'width:150px;', 'class' => 'meeting_row_sixth'));
            $sMeeting.= $this->_oDisplay->getBloc('', $sStatus, array('class' => 'meeting_row_status'));
            if($meetingDoneFlag && isset($meetingInfo['date_met']))
            {
              $updateDate = explode(' ',$meetingInfo['date_met']);
              if(isset($updateDate[0]) && isset($updateDate[1]))
              {
                $sMeeting.= $this->_oDisplay->getBloc('', ' updated '.$updateDate[0].' at '.substr($updateDate[1], 0, 5), array('style' => 'width:auto; margin-left:5px;', 'class' => 'meeting_row_date'));
              }
            }

            $sMeeting.= $this->_oDisplay->getBlocStart('', array('class' => 'meeting_row_action'));
            if($bManager || ($nStatus < 1 && ($nCurrentUser == $oDbResult->getFieldValue('created_by') || $nCurrentUser == $nAttendee)))
            {


              if($nCurrentUser == $nAttendee)
              {
                $sUrl = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_DONE, CONST_CANDIDATE_TYPE_MEETING, $pnCandiPk, array('meetingpk' => $nMeetingPk));
                $asButtons[] = array('url' => '', 'label' => 'Meeting done', 'pic' => $this->getResourcePath().'pictures/done_16.png',
                    'onclick' => 'oConf = goPopup.getConfig(); oConf.width = 1050; oConf.height = 800; goPopup.setLayerFromAjax(oConf, \''.$sUrl.'\');');
              }

              $sUrl = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_EDIT, CONST_CANDIDATE_TYPE_MEETING, $pnCandiPk, array('meetingpk' => $nMeetingPk));
              $asButtons[] = array('url' => '', 'label' => 'Edit meeting', 'pic' => $this->getResourcePath().'pictures/edit_16.png',
                  'onclick' => 'oConf = goPopup.getConfig(); oConf.width = 950; oConf.height = 750; goPopup.setLayerFromAjax(oConf, \''.$sUrl.'\'); ');

              $sUrl = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_SAVEEDIT, CONST_CANDIDATE_TYPE_MEETING, $nMeetingPk, array('fast_edit' => 1, 'status' => -1));
              $asButtons[] = array('url' => '', 'label' => 'Cancel meeting', 'pic' => $this->getResourcePath().'pictures/delete_16.png',
                  'onclick' => 'if(window.confirm(\'Delete this meeting may affect user stats. Continue ?\')){ AjaxRequest(\''.$sUrl.'\'); } ');


              $sMeeting.= $this->_oDisplay->getActionButtons($asButtons, 1, 'Manage meeting...');

            }
            else
            {
              $sMeeting.= '<em class="light italic"> - no action available - </em>';
            }
            $sMeeting.= $this->_oDisplay->getBlocEnd();

          $sMeeting.= $this->_oDisplay->getFloatHack();
          $sMeeting.= $this->_oDisplay->getBlocEnd();

          $asMeeting[$sType][] = $sMeeting;
          $bRead = $oDbResult->readNext();
        }

        if(!empty($asMeeting['active']))
        {
          $sHTML.= $this->_oDisplay->getTitle('Scheduled meetings', 'h3', true);
          $sHTML.= implode('', $asMeeting['active']);
          $sHTML.= $this->_oDisplay->getBloc('', '&nbsp;', array('style' => 'border-top: 1px solid #bbb; '));
        }

        if( !empty($asMeeting['inactive']))
        {
          $sHTML.= $this->_oDisplay->getCR();
          $sHTML.= $this->_oDisplay->getTitle('Past meetings', 'h3', true);
          $sHTML.= implode('', $asMeeting['inactive']);
        }
      }

      $sHTML.= $this->_oDisplay->getBlocEnd();
      return $sHTML;
    }

    private function _getCandidateMeetingForm($pnCandiPk, $pnMeetingPk = 0)
    {

        if(!assert('is_key($pnCandiPk) && is_integer($pnMeetingPk)'))
          return array('error' => 'Sorry, an error occured.');

        $oCandidateData = $this->_getModel()->getByPk($pnCandiPk, 'sl_candidate');
        if(!$oCandidateData)
          return array('error' => 'Sorry, could not fetch the candidate\'s data.');

        $oCandidateData->readFirst();
        $sName = $oCandidateData->getFieldValue('lastname'). ' '.$oCandidateData->getFieldValue('firstname');

        $oPage = CDependency::getCpPage();

        if(!empty($pnMeetingPk))
        {
          $oDbMeeting = $this->_getModel()->getByPk($pnMeetingPk, 'sl_meeting');
          if(!$oDbMeeting || ! $oDbMeeting->readFirst())
            return array('error' => 'Counld not find the meeting.');

          $oForm = $this->_oDisplay->initForm('meetingAddForm');
          $sURL = $oPage->getAjaxUrl($this->csUid, CONST_ACTION_SAVEEDIT, CONST_CANDIDATE_TYPE_MEETING, $pnMeetingPk);

          $oForm->setFormParams('meetingAddForm', true, array('action' => $sURL, 'class' => 'fullPageForm', 'submitLabel'=>'Update meeting', 'noCancelButton' => true));
          $oForm->setFormDisplayParams(array('noCancelButton' => true));
          $oForm->addField('input', 'meetingpk', array('type' => 'hidden','value'=> $pnMeetingPk));
          $oForm->addField('hidden', 'creatorfk', array('value' => $oDbMeeting->getFieldValue('creatorfk')));

          $oForm->addField('misc', '', array('type' => 'title', 'title'=> 'Update meeting with <b>'.$sName.'</b>'));
        }
        else
        {
          $oDbMeeting = new CDbResult();

          $oForm = $this->_oDisplay->initForm('meetingAddForm');
          $sURL = $oPage->getAjaxUrl($this->csUid, CONST_ACTION_SAVEADD, CONST_CANDIDATE_TYPE_MEETING, $pnMeetingPk);

          $oForm->setFormParams('meetingAddForm', true, array('action' => $sURL, 'class' => 'fullPageForm', 'submitLabel'=>'Save meeting'));
          $oForm->setFormDisplayParams(array('noCancelButton' => true));
          $oForm->addField('input', 'meetingpk', array('type' => 'hidden','value'=> 0));
          $oForm->addField('hidden', 'creatorfk', array('value' => $this->casUserData['pk']));

          $oForm->addField('misc', '', array('type' => 'title', 'title'=> 'Set a new meeting...'));
        }

        $oLogin = CDependency::getCpLogin();

        $oForm->addField('hidden', 'candidatefk', array('value' => $pnCandiPk));
        $oForm->addField('hidden', 'pclose', array('value' => getValue('pclose')));
        $oForm->addField('misc', '', array('label' => 'Candidate', 'type' => 'text', 'text' => '#'.$pnCandiPk.' - '.$sName, 'class'  => 'readOnlyField'));

        $nType = (int)$oDbMeeting->getFieldValue('type');
        $oForm->addField('select', 'meeting_type', array('label'=> 'Meeting type'));
        $oForm->addOption('meeting_type', array('label'=> 'In person', 'value' => 1, 'selected' => 'selected'));
        if($nType === 2)
          $oForm->addOption('meeting_type', array('label'=> 'By phone', 'value' => 2, 'selected' => 'selected'));
        else
          $oForm->addOption('meeting_type', array('label'=> 'By phone', 'value' => 2));

        if($nType === 3)
          $oForm->addOption('meeting_type', array('label'=> 'Video chat', 'value' => 3, 'selected' => 'selected'));
        else
          $oForm->addOption('meeting_type', array('label'=> 'Video chat', 'value' => 3));

        $oForm->addOption('meeting_type', array('label'=> 'Other', 'value' => 4));

        $sDate = $oDbMeeting->getFieldValue('date_meeting');
        $sPickerDate = substr($sDate, 0, strlen($sDate)-3);
        if(empty($pnMeetingPk))
          $sJavascript = '';
        else
          $sJavascript = 'if($(this).val() != \''.$sPickerDate.'\'){ $(this).closest(\'form\').find(\'#confirm_changes\').show(0); } ';

        $oForm->addField('input', 'date_meeting', array('type' => 'datetime', 'label'=> 'Meeting date',
          'value' => $sPickerDate, 'onchange' => $sJavascript, 'minDate' => 'now'));

        $oForm->addField('input', 'where', array('type' => 'text', 'label'=> 'Location', 'value' => $oDbMeeting->getFieldValue('location')));

        $sURL = $oPage->getAjaxUrl('login', CONST_ACTION_SEARCH, CONST_LOGIN_TYPE_USER);
        $nAttendee = (int)$oDbMeeting->getFieldValue('attendeefk');
        if(empty($nAttendee))
          $nAttendee = $oLogin->getUserPk();

        $sJavascript = 'if($(this).val() != '.$oLogin->getUserPk().'){ $(this).closest(\'form\').find(\'#notify_attendee_0_Id\').attr(\'checked\', \'checked\'); } ';
        $sJavascript.= 'else { $(this).closest(\'form\').find(\'#notify_attendee_0_Id\').removeProp(\'checked\'); } ';

        if(!empty($pnMeetingPk))
          $sJavascript.= ' if($(this).val() != \''.$nAttendee.'\'){ $(this).closest(\'form\').find(\'#confirm_changes\').show(0); } ';

        $oForm->addField('selector', 'attendee', array('label'=>'Attendees', 'url' => $sURL, 'onchange' => $sJavascript));
        $oForm->setFieldControl('attendee', array('jsFieldTypeIntegerPositive' => ''));

        $oForm->addOption('attendee', array('label' => $oLogin->getUserNameFromPk($nAttendee), 'value' => $nAttendee));


        $oForm->addField('textarea', 'description', array('label'=> 'Description', 'value' => $oDbMeeting->getFieldValue('description')));

        $oForm->addField('checkbox', 'notify_attendee', array('label' => 'Send a notification to attendee when saving'));
        $oForm->addField('checkbox', 'add_reminder1', array('label' => 'Set a reminder  - the day of the meeting'));
        $oForm->addField('checkbox', 'add_reminder2', array('label' => 'Set a reminder  - 2 hours before the meeting'));
        $oForm->addField('checkbox', 'add_reminder3', array('label' => 'Set a reminder  - after the meeting (to update the candidate)'));

        if(!empty($pnMeetingPk))
        {
          $oForm->addSection('confirm_changes', array('class' => 'hidden', 'id' => 'confirm_changes'));
          $oForm->addField('misc', '', array('type' => 'text', 'label' => '', 'text' => '<br /><div style="padding-left: 150px;" class="text_small italic">If the meeting date or attendee change, all the existing reminders will be deleted and new ones will be created.</div>'));
          $oForm->addField('checkbox', 'delete_reminder', array('label' => 'Delete previous reminders'));
          $oForm->closeSection();
        }

        return array('data' => $oForm->getDisplay(), 'error' => '');

    }

    private function _getMeetingDoneForm($pnCandiPk, $pnMeetingPk)
    {
      if(!assert('is_key($pnCandiPk) && is_integer($pnMeetingPk)'))
        return array('error' => 'Sorry, an error occured.');

      $oCandidateData = $this->_getModel()->getByPk($pnCandiPk, 'sl_candidate');
      if(!$oCandidateData)
        return array('error' => 'Sorry, could not fetch the candidate\'s data.');

      $oCandidateData->readFirst();
      $sName = $oCandidateData->getFieldValue('lastname'). ' '.$oCandidateData->getFieldValue('firstname');

      $oPage = CDependency::getCpPage();
      $oLogin = CDependency::getCpLogin();

      $oDbMeeting = $this->_getModel()->getByPk($pnMeetingPk, 'sl_meeting');
      if(!$oDbMeeting || ! $oDbMeeting->readFirst())
        return array('error' => 'Counld not find the meeting.');

      $oForm = $this->_oDisplay->initForm('meetingAddForm');
      $sURL = $oPage->getAjaxUrl($this->csUid, CONST_ACTION_VALIDATE ,CONST_CANDIDATE_TYPE_MEETING, $pnMeetingPk);

      $oForm->setFormParams('meetingAddForm', true, array('action' => $sURL, 'class' => 'fullPageForm', 'submitLabel'=>'Save', 'noCancelButton' => true));
      $oForm->setFormDisplayParams(array('noCancelButton' => true));
      $oForm->addField('hidden', 'creatorfk', array('value' => $oDbMeeting->getFieldValue('created_by')));

      $oForm->addField('misc', '', array('type' => 'title', 'title'=> 'Update meeting status&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<b>#'.$pnCandiPk.' - '.$sName.'</b>'));


      $oLogin = CDependency::getCpLogin();
      $nCreator = (int)$oDbMeeting->getFieldValue('created_by');

      $oForm->addField('input', 'loginfk', array('type' => 'hidden', 'value' => $nCreator));
      $oForm->addField('input', 'candidatefk', array('type' => 'hidden', 'value' => $pnCandiPk));

      $sMessage = '<br /><p id="topTextP">By changing this meeting status to "done", you\'re atomatically changing the candidate status to "met"&sup1; .</p><br/>';
      if($nCreator != $oLogin->getUserPk())
      {
        $sMessage.= '<p id="topTextP2">Plus, you\'ll credit&sup2; this meeting to '.$oLogin->getUserLink($nCreator).' who set the meeting up for you.</p><br/>';
        $oForm->addField('misc', '', array('id'=>'topText','type' => 'text', 'label' => '', 'text' => $sMessage.'<br /><br />'));

        $oForm->addField('checkbox', 'notify_meeting_done', array('id'=>'notifyBox','legend' => 'Notification', 'label' => 'Email '.$oLogin->getUserLink($nCreator).' about this meeting'));
        $oForm->addField('misc', '', array('type' => 'text', 'text' => ''));
      }
      else
        $oForm->addField('misc', '', array('id'=>'topText','type' => 'text', 'label' => '', 'text' => $sMessage.'<br /><br />'));


      // A section to quickly create a note !!
      $nType = (int)$oDbMeeting->getFieldValue('type');
      $oForm->addField('select', 'meeting_type', array('label' => 'Meeting type'));

      $default_date = date('Y-m-d H:i');
      $oForm->addField('input', 'date_met', array('id'=>'meetingDate','type' => 'datetime', 'label'=> 'Meeting date',
        'value' => $default_date, 'minDate' => '-4 day', 'maxDate' => 'now'));

      $oForm->addOption('meeting_type', array('label' => 'In person', 'value' => 1), ($nType === 1));
      $oForm->addOption('meeting_type', array('label' => 'By phone', 'value' => 2), ($nType === 2));
      $oForm->addOption('meeting_type', array('label' => 'Video Chat', 'value' => 3), ($nType === 3));
      $oForm->addOption('meeting_type', array('label' => 'Other', 'value' => 4), ($nType === 4));

      //$oForm->addField('textarea', 'meeting_note', array('label' => 'add a character note'));

      $validCharacterNotes = getSlNotes($pnCandiPk);
      $validCharacterNotesLength = count($validCharacterNotes);

      $characterNoteControlFlag = false;
      if($validCharacterNotesLength > 0)
      {
        $characterNoteControlFlag = true;
      }
      if($characterNoteControlFlag)
      {
        $oForm->addField('textarea', 'meeting_note', array('label' => 'Add a note'));
      }
      else
      {

        $skillArray = array();
        $skillArray['skill_ag'] = '0';
        $skillArray['skill_ap'] = '0';
        $skillArray['skill_am'] = '0';
        $skillArray['skill_mp'] = '0';
        $skillArray['skill_in'] = '0';
        $skillArray['skill_ex'] = '0';
        $skillArray['skill_fx'] = '0';
        $skillArray['skill_ch'] = '0';
        $skillArray['skill_ed'] = '0';
        $skillArray['skill_pl'] = '0';
        $skillArray['skill_e'] = '0';

        $candidate_info = getCandidateInformation($pnCandiPk);

        foreach ($skillArray as $key => $value)
        {
          if(!empty($candidate_info[$key]))
          {
            $skillArray[$key] = $candidate_info[$key];
          }
        }

        $data['skillArray'] = $skillArray;

        /*$addHtml = "
        <div style='margin-left:150px; margim-top:10px;'>
          <table>
            <tr>
             <td style='width:30px !important;' ><p class='spinner_label2'>AG</p></td>
             <td style='width:30px !important;' ><p class='spinner_label2'>AP</p></td>
             <td style='width:30px !important;' ><p class='spinner_label2'>AM</p></td>
             <td style='width:30px !important;' ><p class='spinner_label2'>MP</p></td>
             <td style='width:30px !important;' ><p class='spinner_label2'>IN</p></td>
             <td style='width:30px !important;' ><p class='spinner_label2'>EX</p></td>
             <td style='width:30px !important;' ><p class='spinner_label2'>FX</p></td>
             <td style='width:30px !important;' ><p class='spinner_label2'>CH</p></td>
             <td style='width:30px !important;' ><p class='spinner_label2'>ED</p></td>
             <td style='width:30px !important;' ><p class='spinner_label2'>PL</p></td>
             <td style='width:30px !important;' ><p class='spinner_label2'>E</p></td>
            </tr>
            <tr>
              <td><input type='text' style='width:30px;text-align: center;' name='skill_ag' value='".$skillArray['skill_ag']."'/></td>
              <td><input type='text' style='width:30px;text-align: center;' name='skill_ap' value='".$skillArray['skill_ap']."'/></td>
              <td><input type='text' style='width:30px;text-align: center;' name='skill_am' value='".$skillArray['skill_am']."'/></td>
              <td><input type='text' style='width:30px;text-align: center;' name='skill_mp' value='".$skillArray['skill_mp']."'/></td>
              <td><input type='text' style='width:30px;text-align: center;' name='skill_in' value='".$skillArray['skill_in']."'/></td>
              <td><input type='text' style='width:30px;text-align: center;' name='skill_ex' value='".$skillArray['skill_ex']."'/></td>
              <td><input type='text' style='width:30px;text-align: center;' name='skill_fx' value='".$skillArray['skill_fx']."'/></td>
              <td><input type='text' style='width:30px;text-align: center;' name='skill_ch' value='".$skillArray['skill_ch']."'/></td>
              <td><input type='text' style='width:30px;text-align: center;' name='skill_ed' value='".$skillArray['skill_ed']."'/></td>
              <td><input type='text' style='width:30px;text-align: center;' name='skill_pl' value='".$skillArray['skill_pl']."'/></td>
              <td><input type='text' style='width:30px;text-align: center;' name='skill_e' value='".$skillArray['skill_e']."'/></td>
            </tr>
          </table>
        </div>";

        $oForm->addField('textarea', 'personality_note', array('placeholder'=>'Sections must be filled. Minimum of 25 characters.','label'=>'Personality & Communication', 'isTinymce' => 1));
        $oForm->setFieldControl('personality_note', array('jsFieldMinSize' => '2','jsFieldMaxSize' => 9000));

        $oForm->addField('textarea', 'career_note', array('placeholder'=>'Sections must be filled. Minimum of 25 characters.','label'=>'Career Expertise – Present, Past and Future.', 'isTinymce' => 1));
        $oForm->setFieldControl('career_note', array('jsFieldMinSize' => '2','jsFieldMaxSize' => 9000));

        $oForm->addField('textarea', 'education_note', array('placeholder'=>'Sections must be filled. Minimum of 15 characters.','label'=>'Education & Training', 'isTinymce' => 1));
        $oForm->setFieldControl('education_note', array('jsFieldMinSize' => '2','jsFieldMaxSize' => 9000));

        $oForm->addField('textarea', 'move_note', array('placeholder'=>'Sections must be filled. Minimum of 25 characters.','label'=>'Move – Reason & Timing', 'isTinymce' => 1));
        $oForm->setFieldControl('move_note', array('jsFieldMinSize' => '2','jsFieldMaxSize' => 9000));

        $oForm->addField('textarea', 'compensation_note', array('placeholder'=>'Sections must be filled. Minimum of 15 characters.','label'=>'Compensation Breakdown', 'isTinymce' => 1));
        $oForm->setFieldControl('compensation_note', array('jsFieldMinSize' => '2','jsFieldMaxSize' => 9000));

        $oForm->addField('textarea', 'past_note', array('label'=>'Companies – Recently Met & Introduced', 'isTinymce' => 1));
        $oForm->setFieldControl('past_note', array('jsFieldMinSize' => '2','jsFieldMaxSize' => 9000));*/

        $add_note_html = $this->_oDisplay->render('character_note_add', $data);
        //$oForm->addCustomHtml($addHtml);
        $oForm->addCustomHtml($add_note_html);
      }


      return $oForm->getDisplay().'<br /><span style="float: right; font-style:italic; color: #777; font-size: 85%;" >
        &sup1; Status: candidate ill keep his status unchanged is above met.</br >
        &sup2; KPI: data used to generate set_vs_met KPI</span>';
    }


    private function _getConsultantMeeting($pnLoginPk = 0)
    {
      if(!assert('is_integer($pnLoginPk)'))
        return 'No history available';

      $oLogin = CDependency::getCpLogin();

      $nLoginPk = (int)getValue('loginpk', 0);
      if(!empty($nLoginPk))
        $pnLoginPk = $nLoginPk;

      if(empty($pnLoginPk))
      {
        $pnLoginPk = (int)$this->casUserData['loginpk'];
        $sTitle = 'My meetings ';
      }
      else
        $sTitle = $oLogin->getUserLink($pnLoginPk).'\'s meetings ';

      $asWhere = array();
      $asWhere[] = '(smee.created_by = '.$pnLoginPk.' OR smee.attendeefk = '.$pnLoginPk.') ';

      $sMonth = getValue('month', '');
      if(empty($sMonth) || !is_date($sMonth))
        $sMonth = date('Y-m').'-01';

      $asWhere[] = 'smee.date_meeting >= "'.$sMonth.'" AND smee.date_meeting <= "'.date('Y-m', strtotime('+1 months', strtotime($sMonth))).'-01"';





      $sDateStart = date('Y-m', strtotime('-6 months', strtotime($sMonth))).'-01';
      $sDateEnd = date('Y-m', strtotime('+6 months', strtotime($sMonth))).'-01';
      $asMonthlyMeeting = $this->_getModel()->getMonthlyMeeting($pnLoginPk, $sDateStart, $sDateEnd);


      $sQuery = 'SELECT smee.*, scan.firstname, scan.lastname, IF(meeting_done = 1, 1, 0) as m_done FROM sl_meeting as smee
        INNER JOIN sl_candidate as scan ON (scan.sl_candidatepk = smee.candidatefk) WHERE '. implode(' AND ', $asWhere).'
        ORDER BY m_done, smee.date_meeting DESC ';
      $oDbResult = $this->_getModel()->executeQuery($sQuery);

      //$oDbResult = $this->_getModel()->getByWhere('sl_meeting', implode(' AND ', $asWhere), '', '');
      $bRead = $oDbResult->readFirst();


      $sHTML = $this->_oDisplay->getTitle($sTitle.' - for '.  substr($sMonth, 0, 7), 'h3', true);
      $sHTML.= $this->_oDisplay->getCR(2);
      $sHTML.= $this->_oDisplay->getBlocStart();

      // - - - - - - - - - - - - - - - - - - - - - - - - -
      // left section
      $sHTML.= $this->_oDisplay->getBlocStart('', array('class' => 'meetingListLeft'));

      if(!$bRead)
      {
        $sHTML.= $this->_oDisplay->getBlocMessage('No meeting found in '.substr($sMonth, 0, 7).'.');
      }
      else
      {
        $asMeeting = array();
        $sToday = date('Y-m-d');

        while($bRead)
        {
          $asData = $oDbResult->getData();
          $sURL = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, (int)$asData['candidatefk']);

          $asData['attendee'] = $oLogin->getUserLink((int)$asData['attendeefk']);
          $asData['creator'] = $oLogin->getUserLink((int)$asData['created_by']);
          $asData['candidate'] = '<a href="javascript:;" onclick="view_candi(\''.$sURL.'\'); goPopup.removeLastByType(\'layer\');">#'.$asData['candidatefk'].' - '.$asData['firstname'].' '.$asData['lastname'].'</a>';

          if($asData['meeting_done'] == -1)
          {
            $asData['date_meeting'] = '<span class="strike" title="Cancelled">'.$asData['date_meeting'].'</span>';
          }
          elseif($asData['meeting_done'] == 1)
          {
            $asData['date_meeting'] = '<span class="meeting_list_done" title="Done">'.$asData['date_meeting'].'</span>';
          }
          elseif($asData['date_meeting'] < $sToday)
          {
            $asData['date_meeting'] = '<span class="meeting_list_late" title="Done">'.$asData['date_meeting'].'</span>';
          }

          if($asData['attendeefk'] == $pnLoginPk)
          {
            if($asData['meeting_done'] == 0)
              $asMeeting['incoming'][] = $asData;
            else
              $asMeeting['done'][] = $asData;
          }
          else
            $asMeeting['other'][] = $asData;


          $bRead = $oDbResult->readNext();
        }

        $sTabSelected = 'incoming';
        //$sHeader = 'Meeting ID | Meeting date | Attendee name | Created by | Candidate<br />';

        if(empty($asMeeting['incoming']))
          $asTabs[] = array('label' => 'incoming', 'title' => 'Incoming meetings', 'content' => 'No meetings found');
        else
          $asTabs[] = array('label' => 'incoming', 'title' => 'Incoming meetings ('.count($asMeeting['incoming']).')', 'content' => $this->_getMeetingTabList($asMeeting['incoming']));

        if(empty($asMeeting['done']))
          $asTabs[] = array('label' => 'done', 'title' => 'Passed meetings', 'content' => 'No meetings found');
        else
          $asTabs[] = array('label' => 'done', 'title' => 'Passed meetings ('.count($asMeeting['done']).')', 'content' => $this->_getMeetingTabList($asMeeting['done']));

        if(empty($asMeeting['other']))
          $asTabs[] = array('label' => 'other', 'title' => 'Created for others', 'content' => 'No meetings found');
        else
          $asTabs[] = array('label' => 'other', 'title' => 'Created for others ('.count($asMeeting['other']).')', 'content' => $this->_getMeetingTabList($asMeeting['other']));


        $sHTML.= $this->_oDisplay->getBlocStart('', array('style' => 'float: left;   width: 100%; min-height:450px;'));
        $sHTML.= $this->_oDisplay->getTabs('meeting_tabs', $asTabs, $sTabSelected);
        $sHTML.= $this->_oDisplay->getBlocEnd();
      }

      $sHTML.= $this->_oDisplay->getBlocEnd();


      // - - - - - - - - - - - - - - - - - - - - - - - - -
      // Right section


      $sHTML.= $this->_oDisplay->getBlocStart('', array('class' => 'meetingListRight'));
      /*$sHTML.='<select name="user_list" id="user_list">
        <option value="'.$oLogin->getUserPk().'">Me</option></select><br /><br />';*/


      $sHTML.= $this->_oDisplay->getBlocStart('', array('class' => 'meeting_list_selector'));

      $sURL = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_LIST, CONST_CANDIDATE_TYPE_MEETING);
      $oForm = $this->_oDisplay->initForm();
      $oForm->setFormParams('filterMeeting', true, array('action' => $sURL, 'class' => 'filterMeeting', 'onBeforeSubmit' => 'event.preventDefault();'));
      $oForm->setFormDisplayParams(array('noButton' => true, 'columns' => 1));
        $sURL = $this->_oPage->getAjaxUrl('login', CONST_ACTION_SEARCH, CONST_LOGIN_TYPE_USER);
        $oForm->addField('selector', 'user_list', array('label' => 'Consultant', 'url' => $sURL));
        if($pnLoginPk)
        {
          $sConsultant = strip_tags($oLogin->getUserLink($pnLoginPk));
          $oForm->addOption('user_list', array('label' => $sConsultant, 'value' => $pnLoginPk));
        }

      $sHTML.= $oForm->getDisplay();
      $sHTML.= $this->_oDisplay->getBlocEnd();


      $sHTML.= $this->_oDisplay->getBloc('', 'Month&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>(Incoming/cancelled/done)</span> ', array('class' => 'meetingListRightHeader'));
      $sHTML.= $this->_oDisplay->getBlocStart('', array('class' => 'meetingListRightDate'));

      foreach($asMonthlyMeeting as $sFullDate => $asNumber)
      {
        $sDate = date('M Y', strtotime($sFullDate));

        if($sMonth == $sFullDate)
          $sClass = 'selected';
        else
          $sClass = '';

        $sHTML.= $this->_oDisplay->getBlocStart('', array('class' => 'meeting_filter'));
        if(empty($asNumber['nb_meeting']))
        {

          $sHTML.= $this->_oDisplay->getText($sDate);
        }
        else
        {
          if($asNumber['nb_pending'] > 0)
            $asNumber['nb_pending'] = '<span style="color: orange;">'.$asNumber['nb_pending'].'</span>';

            $sHTML.= $this->_oDisplay->getBlocStart('', array('class' => 'meeting_picker'));
            $sHTML.= $this->_oDisplay->getLink($sDate, 'javascript:;', array('class' => $sClass,
              'value' => $sFullDate,
              'onclick' => '
                $(this).closest(\'.meetingListRightDate\').find(\'a\').removeClass(\'selected\');
                $(this).addClass(\'selected\'); '));
            $sHTML.= $this->_oDisplay->getBlocEnd();

            $sHTML.= $this->_oDisplay->getBloc('', $asNumber['nb_meeting'],  array('class' => 'meeting_total'));
            $sHTML.= $this->_oDisplay->getBloc('','( '.$asNumber['nb_pending'].' / '.$asNumber['nb_cancel'].' / '.$asNumber['nb_done'].' )',  array('class' => 'meeting_split'));
        }

        $sHTML.= $this->_oDisplay->getBlocEnd();
      }

      $sHTML.= $this->_oDisplay->getFloatHack();
      $sHTML.= $this->_oDisplay->getBlocEnd();

      $sURL = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_LIST, CONST_CANDIDATE_TYPE_MEETING);
      $sHTML.= $this->_oDisplay->getCR();
      $sHTML.= $this->_oDisplay->getLink('Filter list', 'javascript:;', array('class' => 'meeting_filter_btn', 'onclick' => '

        //to get the autocomplete to save its value. Submit is prevented
        $(this).parent().find(\'form\').submit();

        var sMonth = $(this).parent().find(\'a.selected\').attr(\'value\');
        var nLoginFk = $(this).parent().find(\'#user_listId\').val();
        var sURL = \''.$sURL.'\' + \'&month=\'+ sMonth + \'&loginpk=\'+ nLoginFk;

        goPopup.removeLastByType(\'layer\');
        ajaxLayer(sURL, 1080, 725);
        '));
      $sHTML.= $this->_oDisplay->getBlocEnd();


      $sHTML.= $this->_oDisplay->getBlocEnd();
      return $sHTML;
    }


    private function _getMeetingTabList($asMeeting)
    {
      $asParam = array('sub_template' => array('CTemplateList' => array(0 => array('row' => 'CTemplateRow'))));
      $oTemplate = $this->_oDisplay->getTemplate('CTemplateList', $asParam);

      //get the config object for a specific template (contains default value so it works without config)
      $oConf = $oTemplate->getTemplateConfig('CTemplateList');
      $oConf->setRenderingOption('full', 'full', 'full');

      $oConf->setPagerTop(false);
      $oConf->setPagerBottom(false);

      $oConf->addColumn('ID', 'sl_meetingpk', array('width' => 40, 'sortable'=> array('javascript' => 1)));
      $oConf->addColumn('Meeting date', 'date_meeting', array('width' => 140, 'sortable'=> array('javascript' => 1)));
      $oConf->addColumn('Attendee', 'attendee', array('width' => 155));
      $oConf->addColumn('Created by', 'creator', array('width' => 155));
      $oConf->addColumn('Candidate', 'candidate', array('width' => 290));

      return $oTemplate->getDisplay($asMeeting);
    }


    /**
     * Save a meeting and create reminders
     *
     * @param integer $pnMeetingPk
     * @return array
     */
    private function _saveMeeting($pnMeetingPk = 0)
    {
      $asTmp = array();
      $asTmp['type'] = (int)getValue('meeting_type');
      $asTmp['created_by'] = (int)getValue('creatorfk');
      $asTmp['candidatefk'] = (int)getValue('candidatefk');

      if(!assert('is_key($asTmp[\'type\']) && is_key($asTmp[\'created_by\']) && is_key($asTmp[\'candidatefk\'])'))
        return array('error' => 'Missing parameters.');

      $asTmp['date_meeting'] = getValue('date_meeting').':00';
      if(empty($asTmp['date_meeting']) || !is_datetime($asTmp['date_meeting']))
        return array('error' => 'Meeting date is not valid. ['.$asTmp['date_meeting'].']');

      $asCandidate = $this->getModel()->getCandidateData($asTmp['candidatefk']);
      if(empty($asCandidate))
        return array('error' => 'Could not fin dthe candidate.');

      if(empty($pnMeetingPk))
      {
        //when creating a meeting, check that the date is > today
        if($asTmp['date_meeting'] < date('Y-m-d') && !(bool)getValue('confirm_date'))
        {
          return array('data' => '', 'action' => 'goPopup.setPopupConfirm(\'Meeting date is set in the past. Is it ok ?\', \' confirmMeetingForm();\', \'\', \'Keep going\', \'\', \'\', 350, 175); ');
        }
      }

      $asTmp['attendeefk'] = (int)getValue('attendee');
      if(empty($asTmp['attendeefk']))
        return array('error' => 'Attendee is not valid.');

      $asTmp['location'] = filter_var(getValue('where'), FILTER_SANITIZE_STRING);
      $asTmp['description'] = purify_html(getValue('description'));
      $asTmp['date_created'] = date('Y-m-d H:i:s');


      //---------------------------------------------------------------------
      //Manage notifications and reminders

      $nTimeMeeting = strtotime($asTmp['date_meeting']);

      //Notify attendee right  now ?
      $sNotify = getValue('notify_attendee');

      //Notification the day before the reminder
      $sReminder = getValue('add_reminder1');
      if(!empty($sReminder))
      {
        $asTmp['date_reminder1'] = date('Y-m-d', strtotime('-1 day', $nTimeMeeting)).' 08:00:00';
      }
      else
        $asTmp['date_reminder1'] = null;

      //Notification 2 hours before before the reminder
      $sReminder = getValue('add_reminder2');
      if(!empty($sReminder))
      {
        $asTmp['date_reminder2'] = date('Y-m-d H:i:s', strtotime('-3 hours', $nTimeMeeting));
      }
      else
        $asTmp['date_reminder2'] = null;


      //Naggy notification once the meeting date is passed
      $sReminder = getValue('add_reminder3');
      if(!empty($sReminder))
      {
        $asTmp['reminder_update'] = date('Y-m-d', strtotime('+1 day', $nTimeMeeting)).' 08:00:00';
      }
      else
        $asTmp['reminder_update'] = null;


      //---------------------------------------------------------------------
      //save the meeting && notify RM
      if(empty($pnMeetingPk))
      {
        $nMeetingPk = $this->_getModel()->add($asTmp, 'sl_meeting');

        //Finally: notify people the candidate status has changed (remove the current user obviosuly)
        $asFollower = $this->_getmodel()->getCandidateRm($asTmp['candidatefk'] , true, false);

        //Do not notify current user or attendee
        if(isset($asFollower[$this->casUserData['loginpk']]))
          unset($asFollower[$this->casUserData['loginpk']]);

        if(isset($asFollower[$asTmp['attendeefk']]))
          unset($asFollower[$asTmp['attendeefk']]);

        if(!empty($asFollower))
        {
          $oLogin = CDependency::getCpLogin();
          $oMail = CDependency::getComponentByName('mail');
          $sURL = $this->_oPage->getUrl('555-001', CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, $asTmp['candidatefk']);

          $sSubject = 'RM alert - Meeting set with #'.$asTmp['candidatefk'];
          $sContent = 'A meeting has just been set up with the candidate <a href="'.$sURL.'">#'.$asTmp['candidatefk'].' - '.$asCandidate['firstname'].' '.$asCandidate['lastname'].
                  '</a> you are following.<br />Meeting planned for the '.$oLogin->getUserLink($asTmp['attendeefk']).' on the '.$asTmp['date_meeting'].' <br /><br />
                    Please access Slistem for more details.';

          foreach($asFollower as $asUserData)
          {
            $sEmail = 'Dear '.$asUserData['name'].', <br /><br />';
            $sEmail.= $sContent;

            $oMail->createNewEmail();
            $oMail->setFrom(CONST_PHPMAILER_DEFAULT_FROM, CONST_CRM_MAIL_SENDER);
            $oMail->addRecipient($asUserData['email'], $asUserData['name']);

            $oMail->send($sSubject, $sEmail);
          }
        }
      }
      else
      {
        return array('error' => 'Edit meeting no ready yet.');
      }

      $asTmp['sl_meetingpk'] = $nMeetingPk;
      $asTmp['candidatefk'] = (int)$asTmp['candidatefk'];
      //
      //Meeting saved ==>  send the notification and reminders
      $this->_addMeetingReminder($asTmp, $sNotify);


      //Meeting all saved... We update the candidate status if needed
      if($asCandidate['statusfk'] < 3)
      {
        $sQuery = 'UPDATE sl_candidate SET statusfk = 3 WHERE statusfk < 3 AND sl_candidatepk = '.$asTmp['candidatefk'];
        $this->_getModel()->executeQuery($sQuery);

        $sURL = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, $asTmp['candidatefk']);

        $this->_getModel()->_logChanges(array('statusfk' => '3'), 'user_history', 'New meeting set for the '.$asTmp['date_meeting'].'.<br /> &rarr; status changed to [Interview set]', '',
              array('cp_uid' =>$this->csUid, 'cp_action' => 'ppae', 'cp_type' => CONST_CANDIDATE_TYPE_CANDI, 'cp_pk' => $asTmp['candidatefk']));
      }

      $sURL = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_MEETING, $asTmp['candidatefk']);

      // By default remove all layers...
      // But only 1 when coming from candidate form
      if((int)getValue('pclose') > 0)
      {
        return array('notice' => 'Meeting saved.', 'action' => '
        goPopup.removeLastByType(\'layer\');
        goPopup.setLayerFromAjax(\'\', \''.$sURL.'\');
        refresh_candi('.$asTmp['candidatefk'].'); ');
      }

      return array('notice' => 'Meeting saved.', 'action' => '
        goPopup.removeByType(\'layer\');
        goPopup.setLayerFromAjax(\'\', \''.$sURL.'\');
        refresh_candi('.$asTmp['candidatefk'].'); ');
    }






    private function _updateMeeting($pnMeetingPk, $pbAjax = false)
    {
      if(!assert('is_key($pnMeetingPk) && is_bool($pbAjax)'))
        return array('error' => __LINE__.' - Wrong parameters.');

      //1. check if the meeting is there
      $oDbResult = $this->_getModel()->getByPk($pnMeetingPk, 'sl_meeting');
      if(!$oDbResult || !$oDbResult->readFirst())
        return array('error' => __LINE__.' - The meeting couldn\'t be found.');

      $asMeeting = $oDbResult->getData();
      $asMeeting['sl_meetingpk'] = (int)$asMeeting['sl_meetingpk'];
      $asMeeting['candidatefk'] = (int)$asMeeting['candidatefk'];
      $asMeeting['created_by'] = (int)$asMeeting['created_by'];

      $oLogin = CDependency::getCpLogin();
      $nCurrentUser = $oLogin->getUserPk();


      if(!$oLogin->isAdmin() &&  !in_array($nCurrentUser, array($asMeeting['attendeefk'], $asMeeting['created_by'])))
        return array('error' => __LINE__.' - Sorry, you can\'t update this meeting: wrong account.');

      $asCandidate = $this->getCandidateData($asMeeting['candidatefk']);
      if(empty($asCandidate) || $asCandidate['_sys_status'] != 0)
        return array('error' => __LINE__.' - Sorry, candidate not available.');


      //========================================================================
      //2. Check for fast edit mode (edit status only)
      //From reminder emails, we'll endup here with status = 1 OR -1 (cancelled)
      if(!$pbAjax || getValue('fast_edit'))
      {
        $nStatus = (int)getValue('status', 0);
        if($nStatus === 1 || $nStatus === -1)
        {
          if((int)$asMeeting['meeting_done'] !== 0 && !getValue('force_update', 0))
          {
            $sURL = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_SAVEEDIT, CONST_CANDIDATE_TYPE_MEETING, $pnMeetingPk, array('status' => $nStatus, 'fast_edit' => 1));
            return array('error' => '<div id="meeting_upd_error">
             <h3>Warning</h3><br />This meeting status has already been updated.
             Are you sure you want to change this meeting status to "<strong>'.(($nStatus===1)? 'done': 'cancelled').'</strong>" ?<br /><br />
             <a href="javascript:;" onclick="AjaxRequest(\''.$sURL.'&force_update=1\'); goPopup.removeActive(true);">Yes</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:;" onclick="goPopup.removeActive(true);">No</a></div>');
          }

          $asUpdate = array('meeting_done' => $nStatus, 'date_updated' => date('Y-m-d H:i:s'));
          if($nStatus === 1)
            $asUpdate['date_met'] = $asUpdate['date_updated'];

          $bUpdated = $this->_getModel()->update($asUpdate, 'sl_meeting', 'sl_meetingpk = '.$pnMeetingPk);
          if(!$bUpdated)
            return array('error' => __LINE__.' - Sorry couldn\'t update the meeting status.');


          // Update candidate status if needed    - - - - - - - - -
          $this->_meetingUpdateCandiStatus($nStatus, $asCandidate, $asMeeting);


          $sURL = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, $asMeeting['candidatefk']);
          $sMeetingURL = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_MEETING, $asMeeting['candidatefk']);

          $sAction = '$(\'#meeting_upd_error\').html(\'Meeting updated successfully.\');
                view_candi(\''.$sURL.'\');
                goPopup.removeByType(\'layer\');
                goPopup.setLayerFromAjax(\'\', \''.$sMeetingURL.'\'); ';

          //page loaded pg=normal, but come again in ajax if need confirm
          return array('data' => $pnMeetingPk.' - Meeting status updated successfully to "<strong>'.(($nStatus===1)? 'done': 'cancelled').'</strong>".
            <script>'.$sAction.'</script>', 'action' => $sAction);
        }
      }
      //========================================================================


      //========================================================================
      //3. standard meeting update
      //check other parameters
      $asNewMeeting = $asMeeting;
      $asNewMeeting['date_updated'] = date('Y-m-d H:i:s');
      $asNewMeeting['meeting_done'] = (int)$asNewMeeting['meeting_done'];

      $asNewMeeting['date_meeting'] = trim(getValue('date_meeting'));
      if(strlen($asNewMeeting['date_meeting']) < 16)
        return array('error' => __LINE__.' - Meeting date seems incomplete.');

      if(strlen($asNewMeeting['date_meeting']) < 19)
        $asNewMeeting['date_meeting'].= ':00';

      if(empty($asNewMeeting['date_meeting']) || !is_datetime($asNewMeeting['date_meeting']) || $asNewMeeting['date_meeting'] == '0000-00-00 00:00:00')
        return array('error' => __LINE__.' - Meeting date is not valid.');

      $sNow = date('Y-m-d H:i:s');
      if($asNewMeeting['date_meeting'] < $sNow && $asMeeting['date_meeting'] > $sNow && !getValue('confirm_date'))
      {
         return array('data' => '', 'action' => 'goPopup.setPopupConfirm(\'Meeting date is beeing changed to a date in the past. Is it ok ?\', \' confirmMeetingForm();\', \'\', \'Keep going\', \'\', \'\', 350, 175); ');
      }

      $asNewMeeting['attendeefk'] = (int)getValue('attendee');
      if(empty($asNewMeeting['attendeefk']))
        return array('error' => __LINE__.' - You need to select an attendee.');

      $asNewMeeting['type'] = (int)getValue('meeting_type', 1);
      $asNewMeeting['description'] = purify_html(getValue('description'));
      $asNewMeeting['location'] = filter_var(getValue('where'), FILTER_SANITIZE_STRING);


      //================================================================================================================
      //================================================================================================================
      //!! If the date or attendee change, we have to delete previous reminders and recreate new ones.
      $sCancelled = '#';
      if($asNewMeeting['date_meeting'] != $asMeeting['date_meeting'] || $asNewMeeting['attendeefk'] != $asMeeting['attendeefk'])
      {

        //check if the checkbox has been checked
        $sConfirm = getValue('delete_reminder');
        if(empty($sConfirm))
          return array('error' => __LINE__.' - Please confirm you agree to delete all the previous reminders.');

        $oNotify = CDependency::getComponentByName('notification');
        $asSource = array(CONST_CP_UID => $this->csUid, CONST_CP_ACTION => CONST_ACTION_VIEW, CONST_CP_TYPE => CONST_CANDIDATE_TYPE_MEETING, CONST_CP_PK => (int)$asMeeting['sl_meetingpk']);

        //---------------------------------------------
        //A. we delete the previous notifications
        $sCancelled = $oNotify->cancelNotification($asSource);


        //---------------------------------------------
        //B. if different recipient and not the current user,  send an email to notify the event is cancelled

        $asSource = array(CONST_CP_UID => $this->csUid, CONST_CP_ACTION => CONST_ACTION_VIEW, CONST_CP_TYPE => CONST_CANDIDATE_TYPE_MEETING, CONST_CP_PK => $asMeeting['sl_meetingpk']);
        $asItem = array(CONST_CP_UID => $this->csUid, CONST_CP_ACTION => CONST_ACTION_VIEW, CONST_CP_TYPE => CONST_CANDIDATE_TYPE_CANDI, CONST_CP_PK => $asMeeting['candidatefk']);
        if($asMeeting['attendeefk'] != $asNewMeeting['attendeefk'] && $asMeeting['attendeefk'] != $nCurrentUser)
        {
          $sId = $oNotify->initNotifier($asSource);
          $sContent = 'The meeting set for the '.$asMeeting['date_meeting'].' with #'.$asMeeting['candidatefk'].' has been cancelled. ';

          $nMeeting = $oNotify->addItemMessage($sId, (int)$asMeeting['attendeefk'], $asItem, $sContent, 'Meeting cancelled');
          if(empty($nMeeting))
          {
            assert('false; // can not add meeting reminder.');
            return array('error' => __LINE__.' - An error occured. Sorry, the meeting has not been saved.');
          }
        }


        //---------------------------------------------
        //C. Re-create new reminders

        //Notify attendee right  now ?
        $sNotify = getValue('notify_attendee');
        $nTimeMeeting = strtotime($asNewMeeting['date_meeting']);

        //Notification the day before the reminder
        $sReminder = getValue('add_reminder1');
        if(empty($sReminder))
          $asNewMeeting['date_reminder1'] = null;
        else
          $asNewMeeting['date_reminder1'] = date('Y-m-d', strtotime('-1 day', $nTimeMeeting)).' 08:00:00';

        //Notification 2 hours before before the reminder
        $sReminder = getValue('add_reminder2');
        if(!empty($sReminder))
          $asNewMeeting['date_reminder2'] = null;
        else
          $asNewMeeting['date_reminder2'] = date('Y-m-d H:i:s', strtotime('-3 hours', $nTimeMeeting));


        //Naggy notification once the meeting date is passed
        $sReminder = getValue('add_reminder3');
        if(empty($sReminder))
          $asNewMeeting['reminder_update'] = null;
        else
          $asNewMeeting['reminder_update'] = date('Y-m-d', strtotime('+1 day', $nTimeMeeting)).' 08:00:00';


        $this->_addMeetingReminder($asNewMeeting, $sNotify);
        //---------------------------------------------
      }


      //================================================================================================================
      //================================================================================================================

      //if nothing opf the above, meeting comes straight from DTA: need to check date format
      if($asNewMeeting['date_reminder1'] == '0000-00-00 00:00:00')
        $asNewMeeting['date_reminder1'] = null;

      if($asNewMeeting['date_reminder2'] == '0000-00-00 00:00:00')
        $asNewMeeting['date_reminder2'] = null;

      if($asNewMeeting['reminder_update'] == '0000-00-00 00:00:00')
        $asNewMeeting['reminder_update'] = null;

      $bUpdated = $this->_getModel()->update($asNewMeeting, 'sl_meeting', 'sl_meetingpk = '.$pnMeetingPk);
      if(!$bUpdated)
         return array('error' => __LINE__.' - An error occured. Sorry, the meeting has not been saved.');


      $sURL = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_MEETING, $asMeeting['candidatefk']);
      return array('notice' => $sCancelled.' - Meeting updated successfully.', 'action' => '
        goPopup.removeByType(\'layer\');
        goPopup.setLayerFromAjax(\'\', \''.$sURL.'\');
        refresh_candi('.(int)$asMeeting['candidatefk'].'); ');
    }



    private function _meetingUpdateCandiStatus($pnStatus, $pasCandidate, $pasMeetingData)
    {
      $bChanged = false;

      //Meeting all saved (done)  ==> update candidate status if needed
      if($pnStatus === 1 && $pasCandidate['statusfk'] < 4)
      {
        //dump($pasMeetingData);

        if($pasMeetingData['type'] == 1)
          $nStatus = 6;
        elseif($pasMeetingData['type'] == 2 || $pasMeetingData['type'] == 3)
          $nStatus = 5;
        else
           $nStatus = 4;

        $sQuery = 'UPDATE sl_candidate SET statusfk = '.$nStatus.' WHERE statusfk < 4 AND sl_candidatepk = '.$pasCandidate['sl_candidatepk'];
        $this->_getModel()->executeQuery($sQuery);

        $sURL = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, $pasCandidate['sl_candidatepk']);

        $this->_getModel()->_logChanges(array('statusfk' => '4'), 'user_history', 'Interview done.<br /> &rarr; Candidate status changed to [ Met ]', '',
              array('cp_uid' =>$this->csUid, 'cp_action' => 'ppae', 'cp_type' => CONST_CANDIDATE_TYPE_CANDI, 'cp_pk' => $pasCandidate['sl_candidatepk']));

        $bChanged = true;
      }

      //Meeting all saved (cancelled)  ==> update candidate status if needed
      if($pnStatus === -1 && $pasCandidate['statusfk'] == 3)
      {
        //check if there are other scheduled meetings
        $oDbResult = $this->_getModel()->getByWhere('sl_meeting', 'candidatefk = '.$pasCandidate['sl_candidatepk'].' AND meeting_done = 0');
        if($oDbResult->numRows() > 0)
          return true;

        //meeting cancelled + no other meeting
        $sQuery = 'UPDATE sl_candidate SET statusfk = 2 WHERE sl_candidatepk = '.$pasCandidate['sl_candidatepk'];
        $this->_getModel()->executeQuery($sQuery);

        $sURL = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, $pasCandidate['sl_candidatepk']);

        $this->_getModel()->_logChanges(array('statusfk' => '4'), 'user_history', 'Meeting cancelled.<br /> &rarr; status changed to [ contacted ]', '',
              array('cp_uid' =>$this->csUid, 'cp_action' => 'ppae', 'cp_type' => CONST_CANDIDATE_TYPE_CANDI, 'cp_pk' => $pasCandidate['sl_candidatepk']));

        $bChanged = true;
      }


      if($bChanged && is_key($pasMeetingData['sl_meetingpk']))
      {
        // remove reminders linked to the meeting
        $oNotification = CDependency::getComponentByName('notification');
        $oNotification->cancelNotification(array('cp_uid' => '555-001', 'cp_action' => 'ppav', 'cp_type' => 'meet','cp_pk' => $pasMeetingData['sl_meetingpk']));
      }

      return true;
    }


    /**
     * Declare a meeting done: update the status. Optional: notify creator and eventually add a note
     * @param integer $pnMeetingPk
     * @return array to be json_encoded
     */
    private function _updateMeetingDone($pnMeetingPk)
    {
      if(!assert('is_key($pnMeetingPk)'))
        return array('error' => 'Could not find the meeting');

      $oLogin = CDependency::getCpLogin();
      $nCandidatefk = (int)getValue('candidatefk');
      $user_id = $oLogin->getUserPk();

      $candidate_info = getCandidateInformation($nCandidatefk);

      $skillArray = array();
      $skillArray[] = $candidate_info['skill_ag'];
      $skillArray[] = $candidate_info['skill_ap'];
      $skillArray[] = $candidate_info['skill_am'];
      $skillArray[] = $candidate_info['skill_mp'];
      $skillArray[] = $candidate_info['skill_in'];
      $skillArray[] = $candidate_info['skill_ex'];
      $skillArray[] = $candidate_info['skill_fx'];
      $skillArray[] = $candidate_info['skill_ch'];
      $skillArray[] = $candidate_info['skill_ed'];
      $skillArray[] = $candidate_info['skill_pl'];
      $skillArray[] = $candidate_info['skill_e'];

      $skillFlag = true;

      foreach ($skillArray as $key => $value)
      {
        if(!isset($value) || $value == null)
        {
          $skillFlag = false;
        }
      }

      $candidate_id = $nCandidatefk;
      $hiddenCharacter = getValue('hiddenCharacter'); //newForm olunca yeni form...

      $validCharacterNotes = getSlNotes($candidate_id);
      $validCharacterNotesLength = count($validCharacterNotes);

      $candidateActiveMeetings = getCandidateActiveMeetings($candidate_id);
      $candidateActiveMeetingsLength = count($candidateActiveMeetings);

      $characterNoteControlFlag = false;
      if($candidateActiveMeetingsLength == 0) // herhangi bir meeting ayarlanmamis ise tek character note
      {
        $characterNoteControlFlag = true;
      }
      $characterNoteControlFlag = false;
      if($validCharacterNotesLength > 0) // ilgili bolumleri iceriyor mu bakmamiz gerekiyor.
      {
        $characterNoteControlFlag = true;
      }

      //character notunu burada eklemek istedik...
      $characterNoteArray = array();
      $addedFlag = true;

      $characterNoteArray['personality_note'] = purify_html(getValue('personality_note'));
      $characterNoteArray['career_note'] = purify_html(getValue('career_note'));
      $characterNoteArray['education_note'] = purify_html(getValue('education_note'));
      $characterNoteArray['move_note'] = purify_html(getValue('move_note'));
      $characterNoteArray['compensation_note'] = purify_html(getValue('compensation_note'));
      $characterNoteArray['past_note'] = purify_html(getValue('past_note'));

      $simpleCharacterNote = purify_html(getValue('meeting_note'));

      $skillValues = array();
      $skillValues['skill_ag'] = getValue('skill_ag');
      $skillValues['skill_ap'] = getValue('skill_ap');
      $skillValues['skill_am'] = getValue('skill_am');
      $skillValues['skill_mp'] = getValue('skill_mp');
      $skillValues['skill_in'] = getValue('skill_in');
      $skillValues['skill_ex'] = getValue('skill_ex');
      $skillValues['skill_fx'] = getValue('skill_fx');
      $skillValues['skill_ch'] = getValue('skill_ch');
      $skillValues['skill_ed'] = getValue('skill_ed');
      $skillValues['skill_pl'] = getValue('skill_pl');
      $skillValues['skill_e'] = getValue('skill_e');

      $oEvent = CDependency::getComponentByName('sl_event');

      $characterNoteFlag = false;
      $characterNote = "";
      $errorArray = '';

      if(!$characterNoteControlFlag)
      {
        foreach ($characterNoteArray as $key => $value)
        {
          if($key == 'past_note' || (isset($value) && !empty($value)))
          {

            /*if($key != 'past_note' && $key != 'education_note' && $key != 'compensation_note' && strlen($value) < 32)
            {//<p></p> icinde geldigi icin +7 ekledik
              return array('error' => __LINE__.' - Please fill all required areas at least 25 characters');
            }*/
            if($key == 'personality_note' && strlen($value) < 25)
            {
              $errorArray .= 'What Does he/she do/Skills? should have 25 characters<br>';
              //return array('error' => __LINE__.' - Personality & Communication should have 25 caracters');
            }
            if($key == 'career_note' && strlen($value) < 25)
            {
              $errorArray .= 'Management and Leadership should have 25 characters<br>';
              //return array('error' => __LINE__.' - Career Expertise – Present, Past & Future should have 25 caracters');
            }
            if($key == 'move_note' && strlen($value) < 25)
            {
              $errorArray .= 'Presence and Communication should have 25 characters<br>';
              //return array('error' => __LINE__.' - Move – Reason & Timing should have 25 caracters');
            }

            if($key == 'education_note' && strlen($value) < 15)
            {
              $errorArray .= 'Major career accomplishments/Education should have 15 characters<br>';
              //return array('error' => __LINE__.' - Education & Training should have 15 caracters');
            }
            if($key == 'compensation_note' && strlen($value) < 15)
            { //<p></p> icinde geldigi icin +7 ekledik
              $errorArray .= 'Career Plan and Compensation should have 15 characters<br>';
              //return array('error' => __LINE__.' - Compensation Breakdown & Desire should have 15 caracters');
            }
            $characterNoteFlag  = true;
            if((isset($value) && !empty($value)))
            {
              $title = str_replace('_',' ',$key);
              $title .= ": ";
              $value = str_replace('<p>','',$value);
              $characterNote .= $title.$value;
            }

          }
          elseif($hiddenCharacter == 'newForm')
          {
            if($key == 'personality_note')
            {
              $errorArray .= 'What Does he/she do/Skills? should have 25 characters<br>';
              //return array('error' => __LINE__.' - Personality & Communication should have 25 caracters');
            }
            elseif($key == 'career_note')
            {
              $errorArray .= 'Management and Leadership should have 25 characters<br>';
              //return array('error' => __LINE__.' - Career Expertise – Present, Past & Future should have 25 caracters');
            }
            elseif($key == 'move_note')
            {
              $errorArray .= 'Presence and Communication should have 25 characters<br>';
              //return array('error' => __LINE__.' - Move – Reason & Timing should have 25 caracters');
            }

            elseif($key == 'education_note')
            {
              $errorArray .= 'Major career accomplishments/Education should have 15 characters<br>';
              //return array('error' => __LINE__.' - Education & Training should have 15 caracters');
            }
            elseif($key == 'compensation_note')
            { //<p></p> icinde geldigi icin +7 ekledik
              $errorArray .= 'Career Plan and Compensation should have 15 characters<br>';
              //return array('error' => __LINE__.' - Compensation Breakdown & Desire should have 15 caracters');
            }
            elseif($key == 'past_note')
            { //<p></p> icinde geldigi icin +7 ekledik
              //$errorArray .= 'Compensation Breakdown & Desire should have 15 characters<br>';
              //return array('error' => __LINE__.' - Compensation Breakdown & Desire should have 15 caracters');
            }
            else
            {
              return array('error' => __LINE__.' - Please fill all required areas.');
            }
          }
          else
          {
            return array('error' => __LINE__.' - The candidate must have 5 character notes. Please fill all required areas.');
          }
        }
        if(!empty($errorArray))
        {
          return array('error' => $errorArray);
        }
        if($characterNoteFlag)
        {
          foreach ($skillValues as $key => $skill)
          {
            if($skill == null || $skill < 1 || $skill > 9)
            {
              return array('error' => __LINE__.' - All skill areas should have a value between 1 - 9');
            }
          }
          foreach ($characterNoteArray as $key => $value)
          {
            if((isset($value) && !empty($value)))
            {
              $array = array();
              $array['candidate_id'] = $candidate_id;
              $array['type'] = $key;
              $array['content'] = $value;
              $array['user_id'] = $user_id;
              insertNote($array);
            }
          }
          updateCandidateSkills($candidate_id,$skillValues);
          $addedFlag = false;
          $characterNoteControlFlag = true;

          $asResult = array();
          $asResult['notice'] = "Activity saved successfully.";
          $asResult['timedUrl'] = CONST_CRM_DOMAIN."/index.php5?uid=555-001&ppa=ppav&ppt=candi&ppk=".$candidate_id."#candi_tab_eventId";
        }
      }
      if(!empty($simpleCharacterNote))
      {
        $asResult = $oEvent->addNote((int)$candidate_id, 'meeting_note', $simpleCharacterNote);

        $addedFlag = false;
        $characterNoteControlFlag = true;
      }

      if(!$characterNoteControlFlag)
      {
        /*$html = "<br><br><br>
                <div style='font-size:20px;'>
                  <strong>Warning!</strong> The candidate should have at least 8 valid<strong>[1]</strong> character notes
                </div>
                <br>
                <div style='font-size:20px;'>
                  Please add character notes to continue.
                </div>
                <br><br><br>
                <div style='font-size:20px; text-decoration: underline;'>
                  <strong>[1] </strong>Each at least 25 (total 200) characters long and added in last 12 months
                </div>";
        return array('data' => $html, 'error' => '');*/
        return array('error' => __LINE__.' - Warning!</strong> The candidate should have 10 valid<strong>[1]</strong> character notes.<br><br>Please add character notes to continue<br><strong>[1] </strong>Each at least 25 (total 250) characters long and added in last 12 months');
      }

      //if($skillFlag)
      if(1) // skill eklemesini buraya tasidigimiz icin gerek kalmadi bu kontrole
      {
        if(!assert('is_key($nCandidatefk)'))
          return array('error' => __LINE__.' - Could not find the candidate data');

        $sNotify = getValue('notify_meeting_done');
        $sNote = trim(getValue('meeting_note'));
        $nCreator = 0;

        if(!empty($sNotify))
        {
          $nCreator = (int)getValue('creatorfk');
          if(!assert('is_key($nCreator)'))
            return array('error' => 'Could not find the meeting creator data.');
        }

        $asCandidate = $this->getCandidateData($nCandidatefk);
        if(empty($asCandidate) || $asCandidate['_sys_status'] > 0)
          return array('error' => __LINE__.' - Could not find the candidate data');

        //field tested, time for update, email and note
        $oMeeting = $this->_getModel()->getByPk($pnMeetingPk, 'sl_meeting');
        $bRead = $oMeeting->readFirst();
        if(!$bRead)
          return array('error' => __LINE__.' - Could not find the meeting data');


        $asMeetingData = array();
        $asMeetingData['meeting_done']= 1;
        $asMeetingData['date_met'] = getValue('date_met', date('Y-m-d H:i:s'));

        if(getValue('meeting_type'))
          $asMeetingData['type'] = (int)getValue('meeting_type');


        $bUpdate = $this->_getModel()->update($asMeetingData, 'sl_meeting', 'sl_meetingpk = '.$pnMeetingPk);
        if(!$bUpdate)
          return array('error' => __LINE__.' - Could not update the meeting');

        $asMeetingData['sl_meetingpk'] = $pnMeetingPk;
        foreach($oMeeting->getData() as $sField => $vValue)
        {
          if(!isset($asMeetingData[$sField]))
            $asMeetingData[$sField] = $vValue;
        }

        $oLogin = CDependency::getCpLogin();
        $nCurrentUser = $oLogin->getUserPk();


        if(!empty($sNotify) /*&& $nCurrentUser != $nCreator*/)
        {
          $asUserData = $oLogin->getUserDataByPk($nCreator);

          if(isset($asUserData))
          {
            $sURL = $this->_oPage->getUrl($this->csUid, CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, $nCandidatefk);
            $sLink = $this->_oDisplay->getLink('#'.$nCandidatefk, $sURL);

            $sContent = 'Dear '.$asUserData['firstname'].',<br /><br />';
            $sContent.= $oLogin->getUserLink($nCurrentUser).' has met the candidate '.$sLink.' thanks to the meeting you\'ve set for him.<br />';
            $sContent.= 'This meeting has been credited to your KPI stats.' ;

            if(!empty($sNote))
            {
              $sContent.= '<br /><br />A character note has been created at this occasion:<br /><br />';
              $sContent.= $sNote;
            }

            $oMail = CDependency::getComponentByName('mail');
            $oMail->createNewEmail();
            $oMail->setFrom(CONST_PHPMAILER_EMAIL, CONST_PHPMAILER_DEFAULT_FROM);

            $oMail->addRecipient($asUserData['email'], $asUserData['lastname'].' '.$asUserData['firstname']);
            $oMail->send('Candidate #'.$nCandidatefk.' - Meeting done', $sContent);
          }
        }

        if(!empty($sNote))
        {
          $oNote = CDependency::getComponentByName('sl_event');
          $asResult = $oNote->addNote($nCandidatefk, 'character', $sNote, $nCurrentUser);

          if(isset($asResult['error']))
            return $asResult;
        }

        $this->_meetingUpdateCandiStatus(1, $asCandidate, $asMeetingData);

        // remove reminders
        $oNotification = CDependency::getComponentByName('notification');
        $oNotification->cancelNotification(array('cp_uid' => '555-001', 'cp_action' => 'ppav', 'cp_type' => 'meet', 'cp_pk' => $pnMeetingPk));

        return array('notice' => 'Meeting updated.', 'action' => 'goPopup.removeByType(\'layer\'); refresh_candi('.$nCandidatefk.'); ');
      }
      else
      {
        return array('error' => __LINE__.' - Please fill all skill areas (AG, FX, AP, etc.) to update the meeting.');
      }
    }


    /**
     * base on the meeting parameters, create the different reminders
     * @param type $pasMeetingData
     * @param type $psNotice
     * @param type $psReminder1
     * @param type $psReminder2
     * @param type $psReminder3
     * @return boolean
     */
    private function _addMeetingReminder($pasMeetingData, $psNotice = '')
    {
      if(!assert('is_array($pasMeetingData) && !empty($pasMeetingData)'))
        return false;


      $oNotify = CDependency::getComponentByName('notification');
      $asSource = array(CONST_CP_UID => $this->csUid, CONST_CP_ACTION => CONST_ACTION_VIEW, CONST_CP_TYPE => CONST_CANDIDATE_TYPE_MEETING, CONST_CP_PK => (int)$pasMeetingData['sl_meetingpk']);
      $sId = $oNotify->initNotifier($asSource);

      $s2Hours = date('Y-m-d H:i:s', strtotime('+2 hours'));
      $sTomorrow = date('Y-m-d').' 23:59:59';

      $pasMeetingData['attendeefk'] = (int)$pasMeetingData['attendeefk'];
      $pasMeetingData['candidatefk'] = (int)$pasMeetingData['candidatefk'];

      //item concerned by the reminder
      $asItem = array(CONST_CP_UID => $this->csUid, CONST_CP_ACTION => CONST_ACTION_VIEW, CONST_CP_TYPE => CONST_CANDIDATE_TYPE_CANDI, CONST_CP_PK => (int)$pasMeetingData['candidatefk']);

      //Notification right now
      if(!empty($psNotice))
      {
        $sReminderText = 'A meeting has been set for you on the '.$pasMeetingData['date_meeting'].' with the candidate #'.$pasMeetingData['candidatefk'];
        $sReminderText.= '<br />Meeting\'s description:<br /><br />'.$pasMeetingData['description'];

        $nReminder = $oNotify->addItemReminder($sId, $pasMeetingData['attendeefk'], $asItem, $sReminderText, 'Meeting notification', date('Y-m-d H:i:s'));
        assert('is_key($nReminder)');
      }

      //Notification the day before the reminder
      if(!empty($pasMeetingData['date_reminder1']) && $pasMeetingData['date_reminder1'] > $sTomorrow)
      {
        $sReminderText = 'You have a meeting set tomorrow with the candidate #'.$pasMeetingData['candidatefk'];
        $sReminderText.= '<br />Meeting\'s description:<br /><br />'.$pasMeetingData['description'];

        $nReminder = $oNotify->addItemReminder($sId, $pasMeetingData['attendeefk'], $asItem, $sReminderText, 'Meeting tomorrow', $pasMeetingData['date_reminder1']);
        assert('is_key($nReminder)');
      }

      //Notification 2 hours before before the reminder

      if(!empty($pasMeetingData['date_reminder2']) && $pasMeetingData['date_reminder2'] > $s2Hours)
      {
        $sReminderText = 'You have a meeting in about 2 hours with the candidate #'.$pasMeetingData['candidatefk'];
        $sReminderText.= '<br />Meeting\'s description:<br /><br />'.$pasMeetingData['description'];

        $nReminder = $oNotify->addItemReminder($sId, $pasMeetingData['attendeefk'], $asItem, $sReminderText, 'Meeting soon', $pasMeetingData['date_reminder2']);
        assert('is_key($nReminder)');
      }


      if(!empty($pasMeetingData['reminder_update']) && $pasMeetingData['reminder_update'] != '0000-00-00 00:00:00')
      {
        //need the meeting pk to create the link
        $sURL = $this->_oPage->getUrl($this->csUid, CONST_ACTION_EDIT, CONST_CANDIDATE_TYPE_CANDI, $pasMeetingData['candidatefk'], array('meeting' => 'met'));
        $sReminderText = 'A meeting was supposed to happen on the '.$pasMeetingData['date_meeting'].' with candidate #'.$pasMeetingData['candidatefk'];
        $sReminderText.= '<br />Please remember to update <a href="'.$sURL.'">the candidate profile</a> and status.';

        /* Updateing meeting --> automatically change candidate status
        $sURL = $this->_oPage->getUrl($this->csUid, CONST_ACTION_FASTEDIT, CONST_CANDIDATE_TYPE_CANDI, $pasMeetingData['candidatefk'], array('meeting' => 'met'));
        $sReminderText.= '<br /><br /> - You can simply change the candidate status to "assessed" by clicking <a href="'.$sURL.'&meetingpk='.$pasMeetingData['sl_meetingpk'].'&meeting_status=1">here</a>.';
        */

        $sURL = $this->_oPage->getUrl($this->csUid, CONST_ACTION_SAVEEDIT, CONST_CANDIDATE_TYPE_MEETING, $pasMeetingData['sl_meetingpk']);
        $sReminderText.= '<br />Update the meeting status:<br /><br />
          - to <a href="'.$sURL.'&status=1">done</a> (will update the candidate status automatically)<br />
          - to <a href="'.$sURL.'&status=-1">cancelled</a> ';

        $sURL = $this->_oPage->getUrl($this->csUid, CONST_ACTION_EDIT, CONST_CANDIDATE_TYPE_MEETING, $pasMeetingData['sl_meetingpk']);
        $sReminderText.= '- <a href="'.$sURL.'&status=0">postpone the meeting</a>.';

        $nReminder = $oNotify->addItemReminder($sId, $pasMeetingData['attendeefk'], $asItem, $sReminderText, 'Update candidate', $pasMeetingData['reminder_update']);
        assert('is_key($nReminder)');
      }

      return true;
    }




    // ====================================================================================
    // ====================================================================================
    // Start CONTACT section


    private function _getCandidateContactForm($pnCandiPk, $pnContactpk = 0, $page_type = "add")
    {
      if(!assert('is_key($pnCandiPk)'))
        return array('error' => 'Sorry, an error occured.');


      $bIsAdmin = (bool)$this->casUserData['is_admin'];

      $candidate_information = $this->_getModel()->getCandidateData($pnCandiPk);

      $oDbResult = $this->_getModel()->getContact($pnCandiPk, 'candi', $this->casUserData['pk'], array_keys($this->casUserData['group']), !$bIsAdmin);
      $bRead = $oDbResult->readFirst();

      $nContact = $oDbResult->numRows();
      $nNewFields = 4 - $nContact;
      if($nNewFields <= 0)
        $nNewFields = 1;

      $nNewFields = 5; // more field needed so we fixed 5 MCA

      $is_creator = false;

      if ($candidate_information['created_by'] == $this->casUserData['loginpk'])
        $is_creator = true;

      $oPage = CDependency::getCpPage();

      $oForm = $this->_oDisplay->initForm('contactAddForm');
      $sURL = $oPage->getAjaxUrl($this->csUid, CONST_ACTION_SAVEADD, CONST_CANDIDATE_TYPE_CONTACT, $pnCandiPk);

      $oForm->setFormParams('addcont', true, array('action' => $sURL, 'class' => 'ContactForm', 'submitLabel'=>'Save contact details'));
      $oForm->setFormDisplayParams(array('noCancelButton' => true, 'columns' => 2));
      $oForm->addField('input', 'candidatepk', array('type' => 'hidden','value'=> $pnCandiPk));
      $oForm->addField('input', 'userfk', array('type' => 'hidden', 'value' => $this->casUserData['pk']));

      $oForm->addField('misc', '', array('type' => 'title', 'title'=> 'Add/edit contact details'));
      $oForm->addField('misc', '', array('type' => 'text', 'text' => ''));


      $asTypes = getContactTypes();

//$sURL = $this->getResourcePath().'/resume/resume_template.html';
//$showURL = $oPage->getAjaxUrl('sl_candidate', CONST_ACTION_ADD, CONST_CANDIDATE_TYPE_CONTACT, $pnCandiPk,0,true);
//$sURL = $oPage->getAjaxUrl('sl_candidate', CONST_ACTION_ADD, CONST_CANDIDATE_TYPE_CONTACT_SHOW, array('pnCandiPk' => $pnCandiPk, 'pnContactpk ' => 0, 'showOld ' => true));
//$showJavascript = 'var oConf = goPopup.getConfig(); oConf.width = 950; oConf.height = 750;  goPopup.setLayerFromAjax(oConf, \''.$showURL.'\'); ';;
//$oForm->addField('misc', '', array('style'=> 'text-align: center','type' => 'text', 'text' => '<a href="#" onclick="alert(`munir alert`)">Click Me</a>'));

      $newArea = 1;
      $nCount = 0;
      if($page_type == "edit")
      {
        while($bRead)
        {
          $asData = $oDbResult->getData();

          $bVisible = $this->check_contact_info_visibility($asData, $this->casUserData, $is_creator);

          if($bVisible)
          {
            $this->_getContactFormRow($oForm, $nCount, $asTypes, $asData);
            $nCount++;
          }

          $bRead = $oDbResult->readNext();
        }
      }

      else
      {
        for($nCount = $nContact; $nCount < $nContact+$nNewFields; $nCount++)
        {
          $this->_getContactFormRow($oForm, $nCount, $asTypes, array(),'',$newArea);
          $newArea++;
        }
      }

      return $oForm->getDisplay();
    }



    private function _getContactFormRow($poForm, $nCount, $asTypes, $pasData, $class = '',$newArea = 0)
    {
      $oLogin = CDependency::getCpLogin();

      if(!empty($pasData))
      {
        $asDefaultparam = array('readonly' => '',
          'style' => 'background-color: #eee;border-color: #e6e6e6; font-style: italic; color: #777;');
      }
      else
        $asDefaultparam = array();

      set_array($pasData['sl_contactpk'], 0);
      set_array($pasData['type'], '');
      set_array($pasData['value'], '');
      set_array($pasData['description'], '');
      set_array($pasData['visibility'], 0);

      if($oLogin->isAdmin())
      {
        //admin can always edit
        $asDefaultparam = array();

        //if edition, add delete box
        if(!empty($pasData['sl_contactpk']))
        {
          $poForm->addField('checkbox', 'delete['.$nCount.']', array('textbefore' => 1, 'label' => 'Delete this row ?', 'value' => (int)$pasData['sl_contactpk']));
          $poForm->addField('misc', '', array('type' => 'text', 'text' => '&nbsp;'));
        }
      }

      if(empty($this->casActiveUser))
      {
        //$this->casActiveUser = CDependency::getCpLogin()->getUserList(0, true, true);
        $this->casActiveUser = $oLogin->getGroupMembers(0, '', true, true);
      }

      if($newArea > 0)
      {
        switch($newArea)
        {
          case 1: $pasData['type'] = 2; break;
          case 2: $pasData['type'] = 5; break;
          case 3: $pasData['type'] = 6; break;
          case 4: $pasData['type'] = 8; break;
          case 5: $pasData['type'] = 3; break;
          default:
            $pasData['type'] = 5; break;
        }
      }
      else if(empty($pasData['type']))
      {
        switch($nCount)
        {
          case 0: $pasData['type'] = 2; break;
          case 1: $pasData['type'] = 5; break;
          case 2: $pasData['type'] = 6; break;
          case 3: $pasData['type'] = 8; break;
          case 4: $pasData['type'] = 3; break;
          default:
            $pasData['type'] = 5; break;
        }
      }

      $pasData['visibility'] = (int)$pasData['visibility'];
      $asParam = $asDefaultparam;
      $asParam['label']= 'Type';
      $poForm->addField('select', 'contact_type['.$nCount.']', $asParam);

      $asParam = $asDefaultparam;
      $asParam['label']= 'Value';
      $asParam['style']= 'padding-left:-500px';
      $asParam['value']= $pasData['value'];
      $poForm->addField('input', 'contact_value['.$nCount.']', $asParam);

      foreach($asTypes as $nType => $asType)
      {
        if($pasData['type'] == $nType)
          $poForm->addOption('contact_type['.$nCount.']', array('value' => $nType, 'label' => $asType['label'], 'selected' => 'selected'));
        else
          $poForm->addOption('contact_type['.$nCount.']', array('value' => $nType, 'label' => $asType['label']));
      }

      $asParam = $asDefaultparam;
      //$asParam['label']= 'Visibility';
      $asParam['class']= 'hidden';
      $asParam['visibility']= 'hidden';
      $asParam['type']= 'hidden';
      $asParam['onchange'] = 'if($(this).val() == 4){ $(\'.custom_vis'.$nCount.'\').fadeIn(); }else { $(\'.custom_vis'.$nCount.':visible\').fadeOut(); } ';
      $poForm->addField('select', 'contact_visibility['.$nCount.']', $asParam,array('type' => 'hidden'));

      if($pasData['visibility'] == 1)
        $poForm->addOption('contact_visibility['.$nCount.']', array('style'=> 'width:5px','value' => 1, 'label' => 'Public', 'selected' => 'selected'));
      else
        $poForm->addOption('contact_visibility['.$nCount.']', array('style'=> 'width:5px','value' => 1, 'label' => 'Public'));

      if($pasData['visibility'] == 2)
        $poForm->addOption('contact_visibility['.$nCount.']', array('style'=> 'width:5px','value' => 2, 'label' => 'Private', 'selected' => 'selected'));
      else
        $poForm->addOption('contact_visibility['.$nCount.']', array('style'=> 'width:5px','value' => 2, 'label' => 'Private'));

      if($pasData['visibility'] == 3)
        $poForm->addOption('contact_visibility['.$nCount.']', array('style'=> 'width:5px','value' => 3, 'label' => 'My team', 'selected' => 'selected'));
      else
        $poForm->addOption('contact_visibility['.$nCount.']', array('style'=> 'width:5px','value' => 3, 'label' => 'My team'));

      if($pasData['visibility'] == 4)
      {
        $poForm->addOption('contact_visibility['.$nCount.']', array('style'=> 'width:5px','value' => 4, 'label' => 'Custom', 'selected' => 'selected'));
        $sClass = '';
      }
      else
      {
        $poForm->addOption('contact_visibility['.$nCount.']', array('style'=> 'width:5px','value' => 4, 'label' => 'Custom'));
        $sClass = ' hidden ';
      }


      $poForm->addField('input', 'sl_contactpk['.$nCount.']', array('type' => 'hidden', 'value' => (int)$pasData['sl_contactpk']));


      //Group management
      $asParam = $asDefaultparam;
      $asParam['label']= 'Quick select';
      $asParam['onchange'] = '

            $(\'#contact_userfk'.$nCount.'Id\').tokenInput(\'clear\');
            $(\'#contact_userfk'.$nCount.'Id\').css(\'color\', \'red\');

            var asCons = $(this).val().split(\'||\');
            //console.log(asCons);
            $(asCons).each(function(nIndex, sValue)
            {
              var asValue = sValue.split(\'@@\');
              if(asValue.length == 2)
              {
                //console.log(\'adding user \'+asValue[1]);
                $(\'#contact_userfk'.$nCount.'Id\').tokenInput(\'add\', {id: asValue[0], name: asValue[1]});
              }
            });  ';

      $poForm->addField('select', 'groupfk'.$nCount, $asParam);
      $poForm->setFieldDisplayParams('groupfk'.$nCount, array('class' => 'custom_vis'.$nCount.$sClass));

      $poForm->addOption('groupfk'.$nCount, array('label' => '-', 'value' => $this->casUserData['loginpk'].'@@'.$this->casUserData['pseudo']));
      foreach($this->casActiveUser as $asUData)
      {
        $asUserList = array();
        foreach($asUData as $asUdetail)
          $asUserList[] = $asUdetail['loginpk'].'@@'.$asUdetail['pseudo'];

        $poForm->addOption('groupfk'.$nCount, array('label' => $asUdetail['group_label'], 'value' => implode('||', $asUserList)));
      }


      $asParam = array();
      $asParam['label']= 'Notes';
      $asParam['value'] = $pasData['description'];
      $asParam['style'] = 'width:510px';
      $poForm->addField('input', 'contact_description['.$nCount.']', $asParam);

      $sURL = $this->_oPage->getAjaxUrl('login', CONST_ACTION_SEARCH, CONST_LOGIN_TYPE_USER, 0, array('show_id' => 0, 'friendly' => 1, 'active_only' => 1));
      $poForm->addField('selector', 'contact_userfk['.$nCount.']', array('type' => 'text', 'label' => 'Users', 'nbresult' => 10, 'url' => $sURL));
      $poForm->setFieldDisplayParams('contact_userfk['.$nCount.']', array('id' => 'user_block_'.$nCount, 'class' => 'custom_vis'.$nCount.$sClass));

      if(!empty($pasData['custom_visibility']))
      {
        $asCustomUser = explode(',', $pasData['custom_visibility']);
        foreach($asCustomUser as $sLoginPk)
          $poForm->addOption('contact_userfk['.$nCount.']', array('value' => (int)$sLoginPk, 'label' => $oLogin->getUserName((int)$sLoginPk, true)));
      }


      $poForm->addField('misc', '', array('type' => 'br'));
    }


    private function _getCandidateContactSave($pbSave = true, $nCandidatePk = 0)
    {
      if(!empty($nCandidatePk))
        $nCandidatePk = $nCandidatePk;
      else
        $nCandidatePk = (int)getValue('candidatepk', 0);


      $edit_flag = true;
      foreach ($_POST['sl_contactpk'] as $key => $value) {
        if($value == 0)
        {
          $edit_flag = false;
        }
      }

      $candidateContactInfoArray = getCandidateContactInfo($nCandidatePk);
      $contactValuesArray = array();

      foreach($candidateContactInfoArray as $key => $value)
      {
        array_push($contactValuesArray, $value['value']);
      }

      $nUserPk = (int)getValue('userfk', 0);
      if(empty($nUserPk))
        $nUserPk = (int)$this->casCandidateData['loginpk'];

      if(empty($nCandidatePk) || empty($nUserPk))
        return array('error' => __LINE__.' - Missing required data.');


      set_array($_POST['contact_value'], array());
      $asContact = array('update' => array(), 'insert' => array(), 'delete' => array());


      $bEmpty = true;

      $controlType = $_POST['contact_type'];
      foreach($_POST['contact_value'] as $nRow => $sValue)
      {
        if(!empty($sValue))
        {
          if($nCandidatePk == 999 && ($controlType[$nRow] == '2' || $controlType[$nRow] == '5' || $controlType[$nRow] == '6'))
          {
            $bEmpty = false;
            break;
          }
        }
      }


      foreach($_POST['contact_value'] as $nRow => $sValue)
      {
        if(!empty($sValue) && $nCandidatePk != 999)
        {
          $bEmpty = false;
          break;
        }
      }

      if($bEmpty)
        return array('error' => 'No contact details (work,mobile or e-mail) input in the form.');

      $bAdmin = $this->_oLogin->isAdmin();

      $nValidRow = 0;
      $anPk = array();
      $asError = array();

      foreach($_POST['contact_value'] as $nRow => $sValue)
      {
        $contact_info = trim($_POST['contact_value'][$nRow]);

        //added to keep crappy data in the database T_T

        if ($edit_flag || !in_array($contact_info, $contactValuesArray))
        {
          if(!$bAdmin && !empty($_POST['sl_contactpk'][$nRow]))
            $sErrorType = 'dba';
          else
            $sErrorType = 'display';

          $_POST['contact_value'][$nRow] = trim($_POST['contact_value'][$nRow]);

          if(empty($sValue) || !empty($_POST['delete'][$nRow]))
          {
            if(isset($_POST['delete'][$nRow]) && !empty($_POST['delete'][$nRow]))
            {
              $asContact['delete'][] = (int)$_POST['delete'][$nRow];
              $nValidRow++;
            }

            unset($_POST['contact_value'][$nRow]);
            unset($_POST['contact_sl_contactpk'][$nRow]);
            unset($_POST['contact_description'][$nRow]);
            unset($_POST['contact_type'][$nRow]);
            unset($_POST['contact_visibility'][$nRow]);
            unset($_POST['contact_userfk'][$nRow]);
          }
          else
          {
            $nValidRow++;

            if(!isset($_POST['contact_type'][$nRow]))
              $_POST['contact_type'][$nRow] = 0;

            if(!isset($_POST['contact_visibility'][$nRow]))
              $_POST['contact_visibility'][$nRow] = 0;

            if(!isset($_POST['contact_userfk'][$nRow]))
              $_POST['contact_userfk'][$nRow] = '';

            if(!isset($_POST['sl_contactpk'][$nRow]))
              $_POST['sl_contactpk'][$nRow] = 0;
            else
              $_POST['sl_contactpk'][$nRow] = (int)$_POST['sl_contactpk'][$nRow];

            if(!isset($_POST['contact_description'][$nRow]))
              $_POST['contact_description'][$nRow] = 0;

            $_POST['contact_type'][$nRow] = (int)$_POST['contact_type'][$nRow];
            $_POST['contact_visibility'][$nRow] = (int)$_POST['contact_visibility'][$nRow];

            //1. controls values
            switch($_POST['contact_type'][$nRow])
            {
              case 1:
              case 2:
              case 4:
              case 6:

                //cleaning data --> crap from [slistem postgresql]
                $_POST['contact_value'][$nRow] = trim(str_replace(array("\n","\r", "\r\n", "\t"), '',  $_POST['contact_value'][$nRow]));
                $sPhone = preg_replace('/[0-9\. \-+()]/', '', $_POST['contact_value'][$nRow]);
                if(!empty($sPhone))
                {
                  $asError[$sErrorType][] = 'Contact row #'.($nRow+1).': phone number ['.$_POST['contact_value'][$nRow].'] contains invalid characters.['.$sPhone.']';
                }
                else
                {
                  $sPhone = preg_replace('/[^0-9]/', '', $_POST['contact_value'][$nRow]);
                  if(strlen($sPhone) < 8)
                    $asError[$sErrorType][] = 'Contact row #'.($nRow+1).': phone number ['.$_POST['contact_value'][$nRow].']  too short.';
                }

                break;

              case 5:

                if(!isValidEmail($_POST['contact_value'][$nRow]))
                  $asError[$sErrorType][] = 'Contact row #'.($nRow+1).':  email ['.$_POST['contact_value'][$nRow].']  isn\'t valid.';

                break;

              case 3:
              case 7:
              case 8:

                if(strtolower(substr($_POST['contact_value'][$nRow], 0, 4)) != 'http')
                  $_POST['contact_value'][$nRow] = 'http://'.$_POST['contact_value'][$nRow];

                if(!isValidUrl($_POST['contact_value'][$nRow]) || !isValidUrl($_POST['contact_value'][$nRow], true))
                {
                  $asError[$sErrorType][] = 'Contact row #'.($nRow+1).':  web url ['.$_POST['contact_value'][$nRow].']  isn\'t valid.';
                }
                else
                {
                  if((int)$_POST['contact_type'][$nRow] == 7)
                  {
                    if(stripos($_POST['contact_value'][$nRow], 'facebook') === false)
                      $asError[$sErrorType][] = 'Contact row #'.($nRow+1).':  facebook url ['.$_POST['contact_value'][$nRow].']  must contain "facebook" in the url.['.$_POST['contact_value'][$nRow].'] ';
                  }

                  if((int)$_POST['contact_type'][$nRow] == 8)
                  {
                    if(stripos($_POST['contact_value'][$nRow], 'linkedin') === false)
                      $asError[$sErrorType][] = 'Contact row #'.($nRow+1).':  linkedin url ['.$_POST['contact_value'][$nRow].']  must contain "linkedin" in the url.['.$_POST['contact_value'][$nRow].'] ';
                  }
                }
                break;

            }


            //2. check visibility
            if($_POST['contact_visibility'][$nRow] == 4)
            {
              if(empty($_POST['contact_userfk'][$nRow]))
                $asError[$sErrorType][] = 'Contact row #'.($nRow+1).':  if visibility is set to "custom", you need to select users.';
            }


            if(empty($_POST['contact_type'][$nRow]) || empty( $_POST['contact_visibility'][$nRow]))
              $asError[$sErrorType][] = 'Contact row #'.($nRow+1).': Contact type and/or visibility invalid.';


            $asTmp = array('sl_contactpk' => $_POST['sl_contactpk'][$nRow],
                  'type' => $_POST['contact_type'][$nRow], 'item_type' => 'candi', 'itemfk' => $nCandidatePk,
                  'date_create' => date('Y-m-d H:i:s'), 'loginfk' => $nUserPk,
                  'value' => filter_var($_POST['contact_value'][$nRow], FILTER_SANITIZE_STRING),
                  'description' => filter_var($_POST['contact_description'][$nRow], FILTER_SANITIZE_STRING),
                  'visibility' => $_POST['contact_visibility'][$nRow],
                  'groupfk' => 0, 'userfk' => $_POST['contact_userfk'][$nRow]);

            if(!empty($_POST['sl_contactpk'][$nRow]))
            {
                $anPk[] = $_POST['sl_contactpk'][$nRow];
                $asContact['update'][] = $asTmp;
            }
            else
              $asContact['insert'][] = $asTmp;
          }
        }

      }

      if(empty($nValidRow))
        return array('error' => 'No contact details to save or already exist... Please input contact details in the "value" field.');

      if(!empty($asError['display']))
        return array('error' => 'The forms contains '.count($asError['display']).' error(s).<br /> - '.implode('<br /> - ', $asError['display']));


      // -=- -=- -=- -=- -=- -=- -=- -=- -=- -=- -=- -=- -=- -=- -=- -=- -=- -=- -=- -=- -=-
      //For existing contact details, send an automatic dba for it to be fixed by the admin.
      if(!empty($asError['dba']))
      {
        $oMail = CDependency::getComponentByName('mail');
        $sURL = $this->_oPage->getUrl('555-001', CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, $nCandidatePk);

        $sSubject = 'Automatic DBA request';
        $sContent = 'Dear Admin,<br /><br />
          Slistem has detected invalid contact details on the candidate profile <a href="'.$sURL.'">#'.$nCandidatePk.'</a>.
          Please take actions based on the following errors:<br /><br /> - '.implode('<br /> - ', $asError['dba']);

        $oMail->createNewEmail();
        $oMail->addRecipient('dba_request@slate.co.jp', 'DBA');
        $oMail->setFrom(CONST_PHPMAILER_EMAIL, CONST_PHPMAILER_DEFAULT_FROM);
        $oMail->send($sSubject, $sContent);
      }

      // -=- -=- -=- -=- -=- -=- -=- -=- -=- -=- -=- -=- -=- -=- -=- -=- -=- -=- -=- -=- -=-
      if(empty($asContact['update']) && empty($asContact['insert']) && empty($asContact['delete']))
        return array('notice' => 'No contact details to save...', 'action' => ' goPopup.removeLastByType(\'layer\'); ');

      if($pbSave)
      {
        // 3.Save contacts details
        if(!empty($asContact['update']))
        {
          // Load the previous contact details. Check if everything is still here
          // and get to know if it's been edited
          //$oDbResult = $this->_getModel()->getByWhere('sl_contact', ' sl_contactpk IN ('.implode(',', $anPk).') ');
          $oDbResult = $this->_getModel()->getContactByPk($anPk);
          $bRead = $oDbResult->readFirst();
          $asPrevious = array();
          while($bRead)
          {
            $asPrevious[$oDbResult->getFieldValue('sl_contactpk')] = $oDbResult->getData();
            $bRead = $oDbResult->readNext();
          }

          foreach($asContact['update'] as $asData)
          {
            if(!isset($asPrevious[$asData['sl_contactpk']]))
              return array('error' => 'Error: Editing a contact detail that doesn\'t exist anymore.');

            $asOldData = $asPrevious[$asData['sl_contactpk']];

            if($asOldData['value'] != $asData['value'] || $asOldData['description'] != $asData['description']
            || $asOldData['visibility'] != $asData['visibility'] || $asOldData['loginfk'] != $asData['userfk']
            || $asOldData['type'] != $asData['type'])
            {
              logUserHistory($this->csUid, CONST_ACTION_EDIT, CONST_CANDIDATE_TYPE_CONTACT, (int)$asData['sl_contactpk'], $asData, true);

              if($asOldData['value'] == $asData['value'] && $asOldData['type'] == $asData['type'] && $asOldData['description'] != $asData['description'])
              {
                //$asData['date_update'] = $asOldData['date_update'];
                //$asData['updated_by'] = $asOldData['updated_by'];
              }
              else
              {
                $asData['date_update'] = date('Y-m-d H:i:s');
                $asData['updated_by'] = $this->casUserData['pk'];
              }

              unset($asData['date_create']);
              unset($asData['loginfk']);

              $this->_getModel()->update($asData, 'sl_contact', 'sl_contactpk = '.$asData['sl_contactpk']);

              //delete  `sl_contact_visibility`
              $this->_getModel()->deleteByFk( (int)$asData['sl_contactpk'], 'sl_contact_visibility', 'sl_contactfk');

              // - - - - - - - - - - - - - - - - - - - -
              //if visibility == 4 (custom) add users in the visibility table
              if($asData['visibility'] == 4 && !empty($asData['userfk']))
              {
                $asViewer = explode(',', $asData['userfk']);
                $asViewerData = array();
                foreach($asViewer as $sViewerPk)
                {
                  $asViewerData['sl_contactfk'][] = (int)$asData['sl_contactpk'];
                  $asViewerData['loginfk'][] = (int)$sViewerPk;
                }

                $this->_getModel()->add($asViewerData, 'sl_contact_visibility');
              }
            }
          }
        }

        foreach($asContact['insert'] as $asData)
        {
          $this->_getModel()->add($asData, 'sl_contact');
        }

        if(!empty($asContact['delete']))
        {
          $this->_getModel()->deleteByWhere('sl_contact', 'sl_contactpk IN('.implode(',', $asContact['delete']).') ');
          $this->_getModel()->deleteByWhere('sl_contact_visibility', 'sl_contactfk IN('.implode(',', $asContact['delete']).') ');
        }
      }

      $sLog = 'Contact details: '.count($asContact['update']).' updated, '.count($asContact['insert']).' added, '.count($asContact['delete']).' deleted';
      $this->_getModel()->_logChanges(array('contact' => 'save'), 'user_history', $sLog, '',
              array('cp_uid' => '555-001', 'cp_action' => 'ppae', 'cp_type' => CONST_CANDIDATE_TYPE_CANDI, 'cp_pk' => $nCandidatePk));

      $sURL = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, $nCandidatePk);
      return array('notice' => 'Contact details saved successfully.', 'action' => 'view_candi("'.$sURL.'", "#tabLink2"); goPopup.removeByType(\'layer\'); ');
    }


    // End CONTACT section
    // ====================================================================================










    // ====================================================================================
    // ====================================================================================
    // start CANDIDATE section
    private function _getCandidateAddForm($pnCandidatePk = 0)
    {
      if(!assert('is_integer($pnCandidatePk)'))
        $pnCandidatePk = 0;

      $bDisplayAllTabs = true;
      $asAttribute = array();
      $parameters = array();

      if(empty($pnCandidatePk))
      {

        $nDuplicateId = (int)getValue('duplicate');
        if(!empty($nDuplicateId))
        {
          $oDbResult = $this->_getModel()->getCandidateFormData($nDuplicateId);
          $oDbResult->readFirst();
          $asClone = $oDbResult->getData();

          $oDbResult = new CDbResult();
          $oDbResult->setFieldValue('companyfk', $asClone['companyfk']);
          $oDbResult->setFieldValue('company_name', $asClone['company_name']);
          $oDbResult->setFieldValue('occupationfk', $asClone['occupationfk']);
          $oDbResult->setFieldValue('industryfk', $asClone['industryfk']);
          $oDbResult->setFieldValue('is_client', $asClone['is_client']);
        }
        else
          $oDbResult = new CDbResult();

      }
      else
      {
        $bDisplayAllTabs = false;
        $oDbResult = $this->_getModel()->getCandidateFormData($pnCandidatePk);
        $oDbResult->readFirst();

        $sAttribute = $oDbResult->getFieldValue('attribute_type');
        if(!empty($sAttribute))
        {
          $asAttributeType = explode(',', $sAttribute);
          $asAttributeValue = explode(',', $oDbResult->getFieldValue('attribute_value'));
          $asAttributeLabel = explode(',', $oDbResult->getFieldValue('attribute_label'));
          foreach($asAttributeType as $nKey => $sValue)
            $asAttribute[$sValue][$asAttributeValue[$nKey]] = $asAttributeLabel[$nKey];
        }

        //Adding a candidate with a $pnCandidatePk ==> duplicate the candidate
        //need to remove all the
        if($this->csAction == CONST_ACTION_ADD)
        {
          $bDisplayAllTabs = true;
          $asToKeep = array('department' => $oDbResult->getFieldValue('department'),
              'companyfk' => (int)$oDbResult->getFieldValue('companyfk'),
              'company_name' => $oDbResult->getFieldValue('company_name'),
              'industryfk' => (int)$oDbResult->getFieldValue('industryfk'));

          $oDbResult = new CDbResult();
          foreach($asToKeep as $sField => $vValue)
            $oDbResult->setFieldValue($sField, $vValue);

          $oDbResult->readFirst();
        }
      }

      $this->_oPage->addJsFile(self::getResourcePath().'js/candidate_form.js');
      $this->_oPage->addJsFile('/component/form/resources/js/currency.js');
      $this->_oPage->addJsFile(array('/component/form/resources/js/jquery.bsmselect.js',
        '/component/form/resources/js/jquery.bsmselect.sortable.js','/component/form/resources/js/jquery.bsmselect.compatibility.js'));

      $this->_oPage->addCssFile(self::getResourcePath().'css/sl_candidate.css');
      $this->_oPage->addCssFile('/component/form/resources/css/jquery.bsmselect.css');
      $this->_oPage->addCssFile('/component/form/resources/css/form.css');
      $this->_oPage->addCssFile('/component/form/resources/css/token-input-mac.css');


      $oForm = $this->_oDisplay->initForm('candidateAddForm');
      $sURL = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_SAVEADD, CONST_CANDIDATE_TYPE_CANDI, $pnCandidatePk);

      $oForm->setFormParams('addcandidate', true, array('action' => $sURL, 'class' => 'candiAddForm', 'submitLabel'=>'Save candidate', 'ajaxTarget' => 'candi_duplicate'));
      $oForm->setFormDisplayParams(array('noCancelButton' => true, /*'noSubmitButton' => 1,*/ 'columns' => 1));


      if($bDisplayAllTabs)
      {
        ini_set('upload_tmp_dir', CONST_PATH_UPLOAD_DIR);
      }

      $contact_details_form = '';

      if(empty($pnCandidatePk) || $this->_oLogin->isAdmin())
        $readonly_name = '';
      else
        $readonly_name = 'readonly';

      $nSex = (int)$oDbResult->getFieldValue('sex');

      $sDate = $oDbResult->getFieldValue('date_birth');
      $sDefaultDate = date('Y', strtotime('-30 years')).'-02-02';
      $sYearRange = (date('Y') - 70).':'.(date('Y') - 12);

      $sYearRangeToday = (date('Y') - 0).':'.(date('Y') - 0);

      $todaysDate = date('Y-m-d');

      $calendar_icon = '//'.CONST_CRM_HOST.'/component/form/resources/pictures/date-icon.png';

      $bEstimated = (bool)$oDbResult->getFieldValue('is_birth_estimation');
      $nAge = date('Y') - date('Y', strtotime($sDate));

      $asCurrency = $this->getVars()->getCurrencies();

      $add_company_url = $this->_oPage->getAjaxUrl(
        $this->csUid, CONST_ACTION_ADD, CONST_CANDIDATE_TYPE_COMP, 0, array('update_field' => '#company',));

      $company_token_url = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_SEARCH, CONST_CANDIDATE_TYPE_COMP, 0);

      if($oDbResult->getFieldValue('companyfk'))
      {
        $company_token = '[{id:"'.$oDbResult->getFieldValue('companyfk').'",
          name:"#'.$oDbResult->getFieldValue('companyfk').' - '.$oDbResult->getFieldValue('company_name').'"}]';
      }
      else
        $company_token = array();

      $occupation_tree = $oForm->getField('paged_tree', 'occupationpk', array('text' => '-- Occupation --',
        'label' => '', 'value' => $oDbResult->getFieldValue('occupationfk'), 'style' => 'width: 165px; min-width: 145px;'));
      $occupation_tree->addOption($this->_getTreeData('occupation'));

      $industry_tree = $oForm->getField('paged_tree', 'industrypk', array('text' => '-- Industry --',
        'label' => '', 'value' => $oDbResult->getFieldValue('industryfk'), 'style' => 'width: 165px; min-width: 145px;'));
      $industry_tree->addOption($this->_getTreeData('industry'));

      $candidate_salary = formatNumber(round($oDbResult->getFieldValue('salary')), $this->casSettings['candi_salary_format']);
      $candidate_salary_bonus = formatNumber(round($oDbResult->getFieldValue('bonus')), $this->casSettings['candi_salary_format']);

      $target_low = formatNumber(round($oDbResult->getFieldValue('target_low')), $this->casSettings['candi_salary_format']);
      $target_high = formatNumber(round($oDbResult->getFieldValue('target_hig')), $this->casSettings['candi_salary_format']);

      $nStatus = 0;
      $bInPlay = false;
      $sDatePlayed = '';
      $asDateMeeting = array('meeting' => '', 'met' => '');

      if(!empty($pnCandidatePk))
      {
        $nStatus = (int)$oDbResult->getFieldValue('statusfk');

        $bInPlay = (bool)$oDbResult->getFieldValue('_in_play');

        if(!$bInPlay)
        {
          $sDatePlayed = (bool)$this->_getModel()->getLastPositionPlayed($pnCandidatePk);

          if(empty($sDatePlayed))
            $asDateMeeting = $this->_getModel()->getLastInterview($pnCandidatePk);
        }
      }

      // * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
      // * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
      // manage status field

      if(CDependency::getCpLogin()->isAdmin())
      {
        $asStatus = '<option value="0"> - </option>
          <option value="1" '.(($nStatus === 1)? ' selected ':'').'> Name Collect </option>
          <option value="2" '.(($nStatus === 2)? ' selected ':'').'> Contacted </option>
          <option value="3" '.(($nStatus === 3)? ' selected ':'').' class="unavailable"> Interview set</option>
          <option value="5" '.(($nStatus === 5)? ' selected ':'').'> Phone assessed </option>
          <option value="6" '.(($nStatus === 6)? ' selected ':'').'> Assessed in person </option>
          <option value="8" '.(($nStatus === 8)? ' selected ':'').'> Lost </option>';
      }
      elseif($bInPlay)
      {
        $asStatus = '
          <option value="2"> Contacted </option>
          <option value="3" class="unavailable"> Interview set</option>
          <option value="5"> Phone assessed </option>
          <option value="6" selected="selected"> Assessed - [ in play ] </option>';
      }
      elseif(!empty($sDatePlayed) || !empty($asDateMeeting['met']))
      {
        if(!empty($sDatePlayed))
          $sLegend = ' previously in play';
        else
          $sLegend = ' candidates met';

        $asStatus = '
          <option value="2" '.(($nStatus === 2)? ' selected ':'').'> Contacted </option>
          <option value="3" class="unavailable"> Interview set</option>
          <option value="5" '.(($nStatus === 5)? ' selected ':'').'> Phone assessed </option>
          <option value="6" '.(($nStatus != 2 && $nStatus != 5)? ' selected ':'').'> Assessed - [ '.$sLegend.' ] </option>';
      }
      else
      {
        if(!empty($asDateMeeting['meeting']) && $nStatus < 3)
        {
          $nStatus = 3;
          $sLegend = ' [ for '.$asDateMeeting['meeting'].' ]';
          $sClass = '';
        }
        else
        {
          $sLegend = '';
          $sClass = ' class="unavailable" ';
        }

        $asStatus = '
          <option value="1" '.(($nStatus === 1)? ' selected ':'').'> Name Collect </option>
          <option value="2" '.(($nStatus === 2)? ' selected ':'').'> Contacted </option>
          <option value="3" '.(($nStatus === 3)? ' selected ':'').' '.$sClass.'> Interview set '.$sLegend.'</option>
          <option value="5" '.(($nStatus === 5)? ' selected ':'').'> Phone assessed </option>
          <option value="6" '.(($nStatus === 6)? ' selected ':'').'> Assessed in person </option>';
          if(CDependency::getCpLogin()->isAdmin())
          {
            $asStatus .= '<option value="8" '.(($nStatus === 8)? ' selected ':'').' '.$sClass.'> Lost </option>';
          }
      }

      $is_client = (int)$oDbResult->getFieldValue('client') + (int)$oDbResult->getFieldValue('is_client');

      if((int)$oDbResult->getFieldValue('skill_ag') == 0)
      {
        $oDbResult->setFieldValue('skill_ag', '-');
        $oDbResult->setFieldValue('skill_ap', '-');
        $oDbResult->setFieldValue('skill_am', '-');
        $oDbResult->setFieldValue('skill_mp', '-');
        $oDbResult->setFieldValue('skill_in', '-');
        $oDbResult->setFieldValue('skill_ex', '-');
        $oDbResult->setFieldValue('skill_fx', '-');
        $oDbResult->setFieldValue('skill_ch', '-');
        $oDbResult->setFieldValue('skill_ed', '-');
        $oDbResult->setFieldValue('skill_pl', '-');
        $oDbResult->setFieldValue('skill_e', '-');
        $spinner_class = ' empty_spinner';
      }
      else
        $spinner_class = '';

      if ($oDbResult->getFieldValue('cpa') && $oDbResult->getFieldValue('mba'))
      {
        $diploma_options = '
          <option value="cpa">CPA</option>
          <option value="mba">MBA</option>
          <option value="both" selected>both</option>
          ';
      }
      else if ($oDbResult->getFieldValue('cpa'))
      {
        $diploma_options = '
          <option value="cpa" selected>CPA</option>
          <option value="mba">MBA</option>
          <option value="both">both</option>
          ';
      }
      else if ($oDbResult->getFieldValue('mba'))
      {
        $diploma_options = '
          <option value="cpa">CPA</option>
          <option value="mba" selected>MBA</option>
          <option value="both">both</option>
          ';
      }
      else
      {
        $diploma_options = '
          <option value="cpa">CPA</option>
          <option value="mba">MBA</option>
          <option value="both">both</option>
          ';
      }

      if (isset($asAttribute['candi_lang']))
        $alt_language = $this->getVars()->getLanguageOption($asAttribute['candi_lang']);
      else
        $alt_language = $this->getVars()->getLanguageOption();

      $alt_occupation_token_url = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_SEARCH, CONST_CANDIDATE_TYPE_OCCUPATION);
      $alt_industry_token_url = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_SEARCH, CONST_CANDIDATE_TYPE_INDUSTRY);

      $alt_occupation_token = $alt_industry_token = '';

      if(isset($asAttribute['candi_occu']))
      {
        foreach($asAttribute['candi_occu'] as $sValue => $sLabel)
        {
          $alt_temp_array[] = '{id: "'.$sValue.'", name: "'.$sLabel.'"}';
          $alt_occupation_token = '['.implode(',', $alt_occupation_array).']';

          $alt_temp_array = '';
        }
      }

      if(isset($asAttribute['candi_indus']))
      {
        foreach($asAttribute['candi_indus'] as $sValue => $sLabel)
        {
          $alt_temp_array[] = '{id: "'.$sValue.'", name: "'.$sLabel.'"}';
          $alt_industry_token = '['.implode(',', $alt_occupation_array).']';
        }
      }

      if($bDisplayAllTabs)
      {
          $oForm->addSection('', array('class' => 'candidate_inner_section'));

          //reuse what ha sbeen done for the standalone form
          $asTypes = getContactTypes();
          for($nCount = 0; $nCount < 4; $nCount++)
          {
            $this->_getContactFormRow($oForm, $nCount, $asTypes, array());
          }

          $oForm->closeSection();

          $contact_details_form = $oForm->getDisplay(true);
      }

      $currency_code = 'jpy';
      $currencyCode = 'jpy';


      if (!empty($oDbResult->getFieldValue('currency')))
      {
        $tmp_currency_code = $oDbResult->getFieldValue('currency');
        if (isset($asCurrency[$tmp_currency_code]))
          $currency_code = $tmp_currency_code;
      }

      if (!empty($oDbResult->getFieldValue('currency')))
      {
        $allData = $oDbResult->getAll();
        if(isset($allData[0]['currency']))
        {
          $currencyCode = $allData[0]['currency'];
        }
      }


      $data = array('currencyCode' => $currencyCode,'form_url' => $sURL, 'user_id' => $this->casUserData['pk'], 'readonly_name' => $readonly_name, 'firstname' => $oDbResult->getFieldValue('firstname'), 'lastname' =>$oDbResult->getFieldValue('lastname'),
        'display_all_tabs' => $bDisplayAllTabs, 'user_sex' => $nSex, 'age_estimate' => $bEstimated,
        'birth_date' => $sDate, 'estimated_age' => '', 'default_date' => $sDefaultDate,'todaysDate' => $todaysDate,
        'language' => $this->getVars()->getLanguageOption($oDbResult->getFieldValue('languagefk')),
        'nationality' => $this->getVars()->getNationalityOption($oDbResult->getFieldValue('nationalityfk')),
        'location' => $this->getVars()->getLocationOption($oDbResult->getFieldValue('locationfk')),
        'add_company_url' => $add_company_url, 'company_token' => $company_token,
        'calendar_icon' => $calendar_icon, 'title' => $oDbResult->getFieldValue('title'),
        'department' => $oDbResult->getFieldValue('department'), 'company_token_url' => $company_token_url,
        'company' => $oDbResult->getFieldValue('companyfk'), 'occupation_tree' => $occupation_tree->getDisplay(),
        'industry_tree' => $industry_tree->getDisplay(), 'candidate_salary' => $candidate_salary,
        'money_unit' => $this->casSettings['candi_salary_format'], 'currency_code' => $currency_code,
        'currency_list' => $asCurrency, 'candidate_salary_bonus' => $candidate_salary_bonus, 'target_low' => $target_low,
        'target_high' => $target_high, 'candidate_id' => $pnCandidatePk, 'status_options' => $asStatus,
        'is_client' => $is_client, 'grade' => $this->getVars()->getCandidateGradeOption($oDbResult->getFieldValue('grade')),
        'diploma_options' => $diploma_options, 'keyword' => $oDbResult->getFieldValue('keyword'), 'spinner_class' => $spinner_class,
        'skill_ag' => $oDbResult->getFieldValue('skill_ag'), 'skill_ap' => $oDbResult->getFieldValue('skill_ap'),
        'skill_am' => $oDbResult->getFieldValue('skill_am'), 'skill_mp' => $oDbResult->getFieldValue('skill_mp'),
        'skill_in' => $oDbResult->getFieldValue('skill_in'), 'skill_ex' => $oDbResult->getFieldValue('skill_ex'),
        'skill_fx' => $oDbResult->getFieldValue('skill_fx'), 'skill_ch' => $oDbResult->getFieldValue('skill_ch'),
        'skill_ed' => $oDbResult->getFieldValue('skill_ed'), 'skill_pl' => $oDbResult->getFieldValue('skill_pl'),
        'skill_e' => $oDbResult->getFieldValue('skill_e'), 'alt_language' => $alt_language,
        'alt_occupation_token_url' => $alt_occupation_token_url, 'alt_industry_token_url' => $alt_industry_token_url,
        'alt_occupation_token' => $alt_occupation_token, 'alt_industry_token' => $alt_industry_token,
        'is_admin' => CDependency::getCpLogin()->isAdmin(), 'candidate_sys_status' => (int)$oDbResult->getFieldValue('_sys_status'),
        'candidate_sys_redirect' => (int)$oDbResult->getFieldValue('_sys_redirect'),
        'contact_details_form' => $contact_details_form, 'year_range' => $sYearRange, 'sYearRangeToday' => $sYearRangeToday
      );


      $sHTML = $this->_oDisplay->render('candidate_add', $data);

      return $sHTML;
    }

    private function _companyDuplicateEscapeWords()
    {
      $escapeWords = array('k.k.','kk','kk.','k.k','inc','inc.','co','co.','co.,','co.,ltd','ltd','ltd.','contracting','consulting','entertainment','japan','tokyo','services','limited','consultants','services','corporation','technologies','systems','company','international','construction','group','engineering','(japan)','ex','(ex','( ex','corp','corp.','(group)','(x)','(ex)','branch','(K.K)','(old)','( old )','(tokyo)','Nippon','Nihon','Kabushiki Kaisha','Kabushiki Gaisha','enterprise','enterprises');

      return $escapeWords;
    }

    public function controlCompanyDuplicate()
    {
      //url
      //https://beta.slate.co.jp/index.php5?uid=555-001&ppa=cdc&ppt=candi&ppk=0&pg=ajx
      $company_name = $_POST['cname'];
      $company_name = TRIM($company_name);
      $company_name = strtolower($company_name);
      //ChromePhp::log($company_name);
      $oDB = CDependency::getComponentByName('database');
      $somthing = true;

      $escapeWords = array('k.k.','kk','kk.','k.k','inc','inc.','co','co.','co.,','co.,ltd','ltd','ltd.','contracting','consulting','entertainment','japan','tokyo','services','limited','consultants','services','corporation','technologies','systems','company','international','construction','group','engineering','(japan)','ex','(ex','( ex','corp','corp.','(group)','(x)','(ex)','branch','(K.K)','(old)','( old )','(tokyo)','nippon','nihon','kabushiki kaisha','kabushiki gaisha','enterprise','enterprises','the');//,'and','&' cikarttim

      $explodedCompanyName = explode(' ',$company_name);
      $nameCount = count($explodedCompanyName);

      $untouchedCompanyNameCount = strlen($company_name);

      if($nameCount == 1)
      {
        $stringCount = strlen($company_name);
        $stringCount = $stringCount;
        /*$sQuery = "SELECT levenshtein('".$company_name."', TRIM(LOWER(slc.name))) AS name_lev, slc.*
                 FROM sl_company slc
                 WHERE levenshtein('".$company_name."', TRIM(LOWER(slc.name))) < 2
                 OR slc.name = '".$company_name."'";*/
        $sQuery = "SELECT IF(LEFT(slc.name , '".$stringCount."') LIKE '".$company_name."', 1, 0) as exact_name2,slc.* FROM sl_company slc WHERE slc.name LIKE '%".$company_name."%' AND slc.merged_company_id = 0 ORDER BY exact_name2 DESC, slc.name ASC";
      }
      else if($nameCount > 1)
      {
        foreach ($explodedCompanyName as $key => $value)
        {
          if (in_array($value, $escapeWords))
          {
            unset($explodedCompanyName[$key]);
          }
        }
        $nameCount = count($explodedCompanyName);
        if($nameCount == 1 && isset($explodedCompanyName[0]))
        {
          $stringCount = strlen($explodedCompanyName[0]);
          $stringCount = $stringCount;
          /*$sQuery = "SELECT levenshtein('".$explodedCompanyName[0]."', TRIM(LOWER(slc.name))) AS name_lev, slc.*
                 FROM sl_company slc
                 WHERE levenshtein('".$explodedCompanyName[0]."', TRIM(LOWER(slc.name))) < 2
                 OR slc.name = '".$explodedCompanyName[0]."' ";*/
          //$sQuery = "SELECT * FROM sl_company slc WHERE slc.name LIKE '%".$explodedCompanyName[0]."%'";
          $sQuery = "SELECT IF(LEFT(slc.name , '".$stringCount."') LIKE '".$explodedCompanyName[0]."', 1, 0) as exact_name2, slc.* FROM sl_company slc WHERE slc.name LIKE '%".$explodedCompanyName[0]."%' AND slc.merged_company_id = 0 ORDER BY  slc.name ASC";
        }
        else
        {
          $implodedName = implode(' ',$explodedCompanyName);
          $stringCount = strlen($implodedName);
          $stringCount = $stringCount;
          /*$sQuery = "SELECT levenshtein('".$company_name."', TRIM(LOWER(slc.name))) AS name_lev, slc.*
                 FROM sl_company slc
                 WHERE ";*/
          $sQuery = "SELECT IF(LEFT(slc.name , '".$untouchedCompanyNameCount."') LIKE '".$company_name."', 1, 0) as exact_name2,slc.* FROM sl_company slc WHERE ( ";
          $addWhere = '';
          foreach ($explodedCompanyName as $key => $value)
          {
            $addWhere .= " slc.name LIKE '%".$value."%' OR ";
            //$addWhere = " levenshtein('".$value."', TRIM(LOWER(slc.name))) < 2 OR slc.name == '".$value."' OR";
          }
          $sQuery .= $addWhere;
          $sQuery = trim($sQuery, "OR ");
          $sQuery .= ") AND slc.merged_company_id = 0 ";
          $sQuery .= " ORDER BY exact_name2 DESC, slc.name ASC";
        }

        $sQuery = trim($sQuery, "OR ");

        //$sQuery .= " OR slc.name LIKE '%".$company_name."%'";

      }
      else
      {
        $somthing = false;
      }
      if($somthing)
      {
        $sQuery = trim($sQuery, "OR ");
        $sQuery = trim($sQuery, "OR");
        $sQuery .= " LIMIT 200";
        ChromePhp::log($sQuery);

        $db_result = $oDB->executeQuery($sQuery);

        $result = $db_result->getAll();

        $company_list = "";
        $adet = count($result);
        //ChromePhp::log($adet);
        if($adet > 0)
        {
          foreach ($result as $key => $value)
          {
            $company_list.= "&#x25cf; ".$value['name']." (#".$value['sl_companypk'].")".",<br>";
            //$company_list.= $value['sl_companypk']."-".$value['name']."_";
          }
          $company_list = trim($company_list, ",<br>");
        }
        else
        {
          $company_list = "none";
        }
      }
      else
      {
        $company_list = "none";
      }
      //ChromePhp::log($company_list);


      //$company_list = "test (#123456), Test (#123456)";

      $jsonData = json_encode($company_list);
      //ChromePhp::log($jsonData);
      return $jsonData;
      //ChromePhp::log($result);
      //return 'RESULT';

      //$possibleDuplicates = getDuplicateCompanies($company_name);
      //ChromePhp::log($possibleDuplicates);
      //echo 'asdasdasd';

    }

    private function _getCompanyForm($pnPk = 0)
    {
      $testUrl = $this->_oPage->getAjaxUrl($this->csUid, COMPANY_DUPLI_CONTROL, CONST_CANDIDATE_TYPE_CANDI);
      ChromePhp::log($testUrl);


      if(!assert('is_integer($pnPk)'))
        return '';

      if(isset($_GET['cid']) && !empty($_GET['cid']))
      {
        $pnPk = $_GET['cid'];
      }
      $changeOwnerFlag = false;
      $asCompanyData = array();

      if(empty($pnPk))
      {
        $asCompanyData['level'] = 1;
        $asCompanyData['is_client'] = 0;
        $asCompanyData['name'] = '';
        $asCompanyData['corporate_name'] = '';
        $asCompanyData['industrypk'] = 0;
        $asCompanyData['description'] = '';

        $asCompanyData['revenue'] = '';
        $asCompanyData['hq'] = '';
        $asCompanyData['hq_japan'] = '';
        $asCompanyData['num_employee_world'] = '';
        $asCompanyData['num_employee_japan'] = '';
        $asCompanyData['num_branch_japan'] = '';
        $asCompanyData['num_branch_world'] = '';

        $asCompanyData['phone'] = '';
        $asCompanyData['fax'] = '';
        $asCompanyData['email'] = '';
        $asCompanyData['website'] = '';
      }
      else
      {
        $changeOwnerFlag = true;
        //$asCompanyData = $this->_getModel()->getCompanyData($pnPk, true);
        $asCompanyData = getCompanyInfo($pnPk);
        $allCompanyDataWithMultipleIndustries = $asCompanyData;

        $asCompanyData = $asCompanyData[0];// burada birden fazla obje geliyor industry fazla olunca hepsi icin ayri bir satir donuyor
        $asCompanyData['industry'] = array();
        foreach ($allCompanyDataWithMultipleIndustries as $key => $value)
        {
           $asCompanyData['industry'][] = $value['indus_name'];
           $asCompanyData['industry_id'][] = $value['sl_industrypk'];
        }
        if(empty($asCompanyData))
          return 'Could not find the company.';

      }

      $sUpdateField = getValue('update_field', '');

      $oForm = $this->_oDisplay->initForm('companyAddForm');
      $sURL = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_SAVEADD, CONST_CANDIDATE_TYPE_COMP, $pnPk);

      $oForm->setFormParams('addcompany', true, array('action' => $sURL, 'class' => 'companyAddForm', 'submitLabel'=>'Save company'));
      //'onBeforeSubmit' => "beforeCompanyAdd(this);" boyleydi
      $oForm->setFormDisplayParams(array('onclick' => 'beforeCompanyAdd();','class' => 'CompanyAddBtn','noCancelButton' => true, /*'noSubmitButton' => 1,*/ 'columns' => 1));


      $oForm->addField('input', 'loginfk', array('type' => 'hidden', 'value' => $this->casUserData['pk']));
      $oForm->addField('input', 'update_field', array('type' => 'hidden', 'value' => $sUpdateField));

      if(empty($sUpdateField))
        $oForm->addField('misc', '', array('type' => 'title', 'title'=> 'Add/edit company details'));
      else
        $oForm->addField('misc', '', array('type' => 'title', 'title'=> 'Add a company - you will be back to the candidate form afterward.'));


      $selectA = '';
      $selectB = '';
      $selectC = '';
      $selectH = '';
      $select01 = "";
      $select0 = "";

      $selectA1 = "";
      $selectB1 = "";
      $selectC1 = "";
      $selectH1 = "";

      if($asCompanyData['level'] == 1)
      {
        $selectA1 = "selected";
        $selectA = "selected";
      }
      else if($asCompanyData['level'] == 2)
      {
        $selectB1 = "selected";
        $selectB = "selected";
      }
      else if($asCompanyData['level'] == 3)
      {
        $selectC1 = "selected";
        $selectC = "selected";
      }
      else if($asCompanyData['level'] == 8)
      {
        $selectH1 = "selected";
        $selectH = "selected";
      }
      else
      {
        $select01 = "selected";
        $select0 = "selected";
      }

      $is_client1Y = '';
      $is_client2Y = '';
      $is_client1N = '';
      $is_client2N = '';

      $is_ns1Y = '';
      $is_ns2Y = '';
      $is_ns1N = '';
      $is_ns2N = '';

      if($asCompanyData['is_client'] == 1)
      {
        $is_client1Y = 'selected';
        $is_client2Y = 'selected';
      }
      else
      {
        $is_client1N = 'selected';
        $is_client2N = 'selected';
      }

      if(isset($asCompanyData['is_nc_ok']) && $asCompanyData['is_nc_ok'] == 1)
      {
        $is_ns1Y = 'selected';
        $is_ns2Y = 'selected';
      }
      else
      {
        $is_ns1N = 'selected';
        $is_ns2N = 'selected';
      }

       $oForm->addField('select', 'level', array('label'=> 'Level'));
       $oForm->addoption('level', array('label' => 'A', 'value' => '1', $selectA1 => $selectA));
       $oForm->addoption('level', array('label' => 'B', 'value' => '2', $selectB1 => $selectB));
       $oForm->addoption('level', array('label' => 'C', 'value' => '3', $selectC1 => $selectC));
       $oForm->addoption('level', array('label' => 'H', 'value' => '8', $selectH1 => $selectH));
       $oForm->addoption('level', array('label' => '-', 'value' => '0', $select01 => $select0));

       $oForm->addField('select', 'is_client', array('label'=> 'Client '));
       $oForm->addoption('is_client', array('label' => 'No', 'value' => '0', $is_client1N => $is_client2N));
       $oForm->addoption('is_client', array('label' => 'Yes', 'value' => '1', $is_client1Y => $is_client2Y));

       $oForm->addField('select', 'is_nc_ok', array('label'=> 'Name collect OK? '));
       $oForm->addoption('is_nc_ok', array('label' => 'No', 'value' => '0', $is_ns1N => $is_ns2N));
       $oForm->addoption('is_nc_ok', array('label' => 'Yes', 'value' => '1', $is_ns1Y => $is_ns2Y));

       $activeUserList = getActiveUsers();

       $oForm->addField('select', 'company_owner_new', array('label'=> 'New owner '));
       $oForm->addoption('company_owner_new',array( 'value' => '0'));
       foreach ($activeUserList as $key => $user)
       {
         $userFullName = $user['firstname'].' '.$user['lastname'];
         $newOwnerValue = $user['loginpk'].'_'.$pnPk;
         $oForm->addoption('company_owner_new',array('label' => $userFullName, 'value' => $newOwnerValue));
       }

       if($changeOwnerFlag)
       {
          $owners = getCompanyOwner($pnPk);
          /*if(!empty($owners))
          {
            foreach ($owners as $key => $value)
            {
              ChromePhp::log($value);
            }
          }*/

          $i=0;
          foreach ($owners as $key => $value)
          {
            $i++;
            $oForm->addField('select', 'company_owner_'.$i, array('label'=> 'Owner '.$i));
            foreach ($activeUserList as $key => $user)
            {
              $userFullName = $user['firstname'].' '.$user['lastname'];
              $optionValue = $user['loginpk'].'_'.$value['id']; //kullanicicnin id si _ owner tablosundaki id
              if($user['loginpk'] == $value['owner'])//$asCompanyData['company_owner'] idi multi yapinca degistirdk
              {
                $oForm->addoption('company_owner_'.$i, array('label' => $userFullName, 'value' => $optionValue, 'selected' => 'selected'));
              }
              else
              {
                $oForm->addoption('company_owner_'.$i,array('label' => $userFullName, 'value' => $optionValue));
              }
            }
            $deleteOptionValue = '000_'.$value['id'];
            $oForm->addoption('company_owner_'.$i,array('style' => 'color:red;font-weight: bold;','label' => 'DELETE', 'value' => $deleteOptionValue));
          }
       }

       $oForm->addField('input', 'company_name', array('class'=> 'companyNameClass','label'=> 'Company name','value' => $asCompanyData['name']));
       $oForm->setFieldControl('company_name', array('jsFieldNotEmpty' => '', 'jsFieldMinSize' => '2'));

       $oForm->addField('input', 'corporate_name', array('label'=> 'Brand / Public name', 'value' => $asCompanyData['corporate_name']));

       //$oForm->addField('paged_tree', 'industrypk', array('text' => ' -- Industry --', 'label' => 'industry', 'value' => $oDbResult->getFieldValue('industryfk')));
       //$oForm->addoption('industrypk', $this->_getTreeData('industry'));

       $sURL = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_SEARCH, CONST_CANDIDATE_TYPE_INDUSTRY);
       $oForm->addField('selector', 'industrypk', array('label' => 'Industries', 'url' => $sURL, 'nbresult' => 10));
       if(!empty($asCompanyData['industry']))
       {
         foreach($asCompanyData['industry'] as $nKey => $sIndustry)
         {
          $oForm->addoption('industrypk', array('label' => $sIndustry, 'value' => $asCompanyData['industry_id'][$nKey]));

           //$oForm->addoption('industrypk', array('label' => $sIndustry, 'value' => $asCompanyData['industry_id'][$nKey]));
         }
       }


      $oForm->addField('textarea', 'description', array('label'=> 'Description', 'value' => $asCompanyData['description']));


      $oForm->addSection('', array('folded' => 1), 'Structure & employees');

      $oForm->addField('input', 'revenue', array('label'=> 'Annual revenue', 'value' => $asCompanyData['revenue']));
      $oForm->setFieldControl('revenue', array('jsFieldMinSize' => '2'));

      $oForm->addField('input', 'hq', array('label'=> 'HQ', 'value' => $asCompanyData['hq']));
      $oForm->addField('input', 'hq_japan', array('label'=> 'HQ in japan', 'value' => $asCompanyData['hq_japan']));

      $oForm->addField('misc', '', array('type'=> 'br'));

      $oForm->addField('input', 'num_employee', array('label'=> '# employees ', 'value' => $asCompanyData['num_employee_world']));
      $oForm->setFieldControl('num_employee', array('jsFieldTypeIntegerPositive' => '1'));

      $oForm->addField('input', 'num_branch_world', array('label'=> '# branch(es)', 'value' => $asCompanyData['num_branch_world']));
      $oForm->setFieldControl('num_branch_world', array('jsFieldTypeIntegerPositive' => '1'));

      $oForm->addField('input', 'num_employee_japan', array('label'=> '# employees in japan', 'value' => $asCompanyData['num_employee_japan']));
      $oForm->setFieldControl('num_employee_japan', array('jsFieldTypeIntegerPositive' => '1'));

      $oForm->addField('input', 'num_branch_japan', array('label'=> '# branch(es) in japan', 'value' => $asCompanyData['num_branch_japan']));
      $oForm->setFieldControl('num_branch_japan', array('jsFieldTypeIntegerPositive' => '1'));
      $oForm->closeSection();


       $oForm->addSection('', array('folded' => 1), 'Contact details');

       $oForm->addField('input', 'phone', array('label'=> 'Phone', 'value' => $asCompanyData['phone']));
       $oForm->addField('input', 'fax', array('label'=> 'Fax', 'value' => $asCompanyData['fax']));
       $oForm->addField('input', 'email', array('label'=> 'Email', 'value' => $asCompanyData['email']));
       $oForm->addField('input', 'website', array('label'=> 'website', 'value' => $asCompanyData['website']));
       $oForm->closeSection();

       if($pnPk == 0)// sadece new company ise bakiorz edit ise bakmayacagiz
       {
          $customHtml = '<div id="dialog" title="Alert message" style="display: none">
                <div class="ui-dialog-content ui-widget-content">
                    <p>
                        <span style="float: left; "></span>
                        <label id="lblMessage">
                        </label>
                    </p>
                </div>
            </div>
            <script>
              $("input[name=Save_company]").prop("type", "button");
              $("input[name=Save_company]").attr("onclick", "beforeCompanyAdd();");
            </script>';

          $oForm->addCustomHtml($customHtml);
       }

      return $oForm->getDisplay();
    }

    private function _saveCompany($pnPk)
    {
      if(!assert('is_integer($pnPk)'))
        return array('error' => 'bad parameters.');

      $oLogin = CDependency::getCpLogin();
      $user_id = $oLogin->getUserPk();

      $mailFlag = 'normal';
      if(isset($_GET['mailFlg']))
      {
        $mailFlag = $_GET['mailFlg'];
      }
ChromePhp::log($mailFlag);
      if($mailFlag == 'yes' || $mailFlag == 'normal')
      {

        $asData = array();
        $asData['name'] = filter_var(getValue('company_name'), FILTER_SANITIZE_STRING);
        $asData['corporate_name'] = filter_var(getValue('corporate_name'), FILTER_SANITIZE_STRING);
        $asData['description'] = filter_var(getValue('description'), FILTER_SANITIZE_STRING);
        $asData['level'] = (int)getValue('level');
        $asData['is_client'] = (int)getValue('is_client');

        $asData['phone'] = filter_var(getValue('phone', null), FILTER_SANITIZE_STRING);
        $asData['fax'] = filter_var(getValue('fax', null), FILTER_SANITIZE_STRING);
        $asData['email'] = filter_var(getValue('email', null), FILTER_SANITIZE_EMAIL);
        $asData['website'] = filter_var(getValue('website', null), FILTER_SANITIZE_URL);

        $asData['revenue'] = getValue('revenue');
        $asData['hq'] = filter_var(getValue('hq', null), FILTER_SANITIZE_STRING);
        $asData['hq_japan'] = filter_var(getValue('hq_japan', null), FILTER_SANITIZE_STRING);

        $asData['num_employee_world'] = (int)getValue('num_employee', 0);
        $asData['num_branch_world'] = (int)getValue('num_branch_world', 0);

        $asData['num_employee_japan'] = (int)getValue('num_employee_japan', 0);
        $asData['num_branch_japan'] = (int)getValue('num_branch_japan', 0);


        $nLoginFk = (int)getValue('loginfk');

        if(empty($pnPk))
        {
          $bUpdate = false;

          $asData['date_created'] = date('Y-m-d H:i:s');
          $asData['created_by'] = $nLoginFk;
          $asData['company_owner'] = $nLoginFk;
          $pnPk = $this->_getModel()->add($asData, 'sl_company');
          if(empty($pnPk))
          {
            return array('error' => 'Could not save the company.');
          }
          if(isset($mailFlag) && $mailFlag == 'yes')
          {
            $to = "rkiyamu@slate.co.jp";
            $subject = "Possible duplication!";
            $message = "Possible duplication for company id #".$pnPk;
            sendHtmlMail($to,$subject, $message);
          }
        }
        else
        {
          $bUpdate = true;

          $asData['date_updated'] = date('Y-m-d H:i:s');
          $asData['updated_by'] = $nLoginFk;
          $asData['company_owner'] = (int)getValue('company_owner');
          $asData['is_nc_ok'] = (int)getValue('is_nc_ok');
          $bUpdated = $this->_getModel()->update($asData, 'sl_company', 'sl_companypk = '.$pnPk);
          if(!$bUpdated)
            return array('error' => 'Could not update the company.');

          //$company_owners = array();
          $i=1;
          $field_name = "company_owner_".$i;
          $company_owner = getValue($field_name);

          while(isset($company_owner) && !empty($company_owner))
          {
            $company_owner = explode('_',$company_owner);
            $newOwner = $company_owner[0];
            $changeID = $company_owner[1];

            if($newOwner == '000')//DELETE
            {
              deleteClientOwner($changeID, $user_id);
            }
            else
            {
              updateCompanyOwner($newOwner,$user_id,$changeID);
            }

            $i++;
            $field_name = "company_owner_".$i;
            $company_owner = getValue($field_name);
          }

          $newCompanyOwner = getValue('company_owner_new');
          if(isset($newCompanyOwner) && !empty($newCompanyOwner) && $newCompanyOwner != '0')
          {
            $newCompanyOwner = explode('_',$newCompanyOwner);
            $newOwner = $newCompanyOwner[0];
            $company_id = $newCompanyOwner[1];
            insertNewOwner($newOwner,$user_id,$company_id);
          }
          //ChromePhp::log($company_owners);
        }

        $asIndustry = explode(',', getValue('industrypk'));
        $asInsertIndus = array();
        $sNow = date('Y-m-d H:i:s');
        foreach($asIndustry as $nKey => $sIndustryKey)
        {
          $sIndustryKey = (int)$sIndustryKey;
          if(!empty($sIndustryKey))
          {
            $asInsertIndus['itemfk'][$nKey] = $pnPk;
            $asInsertIndus['attributefk'][$nKey] = (int)$sIndustryKey;
            $asInsertIndus['type'][$nKey] ='cp_indus';
            $asInsertIndus['loginfk'][$nKey] = $nLoginFk;
            $asInsertIndus['date_created'][$nKey] = $sNow;
          }
        }

        //if the array ios not empty, we need to save the industry
        if(!empty($asInsertIndus))
        {
          if($bUpdate)
            $this->_getModel()->deleteByWhere('sl_attribute', '`type` = \'cp_indus\' AND itemfk='.$pnPk);

          $nInserted = $this->_getModel()->add($asInsertIndus, 'sl_attribute');
          if(empty($nInserted))
            return array('error' => 'Could not save the company industry.');
        }

        //form opened from candidate form,
        //need to update the company field in the form when when the company is saved
        $sUpdateField = getValue('update_field', '');
        if($sUpdateField)
        {
          if(isset($asInsertIndus['attributefk']))
          {
            $anPK = array_values($asInsertIndus['attributefk']);
            $sPreSelectJs = '
            if($(\'.fieldNameindustrypk input[name=industrypk]\').val()<= 0)
            {
              $(\'.fieldNameindustrypk li[sl_industrypk='.$anPK[0].']\').click();
            }';
          }
          else
            $sPreSelectJs = '';

          return array('data' => 'ok',
            'action' => '$(\''.$sUpdateField.'\').val('.$pnPk.');
            $(\''.$sUpdateField.'\').tokenInput(\'clear\').tokenInput(\'add\', {id: \''.$pnPk.'\', name: \''.addslashes($asData['name']).'\'}); '.$sPreSelectJs.' goPopup.removeLastByType(\'layer\'); ');
        }

        $sURL = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_COMP, $pnPk);
        return array('notice' => 'Company saved.', 'action' => 'view_comp(\''.$sURL.'\'); goPopup.removeLastByType(\'layer\'); ');
      }
      else
      {
        return array('error' => 'You cancelled adding a new company.');
      }
    }


    private function _getCompanyList($poQB = null)
    {
      global $gbNewSearch;

      $oLogin = CDependency::getCpLogin();

      $asListMsg = array();
      $bFilteredList = (bool)getValue('__filtered');

      $nHistoryPk = (int)getValue('replay_search');
      if($nHistoryPk > 0)
      {
        $this->csSearchId = getValue('searchId');
        //$asListMsg[] = 'replay search '.$nHistoryPk.': reload qb saved in db...';

        $asHistoryData = $oLogin->getUserActivityByPk($nHistoryPk);
        $oQb = $asHistoryData['data']['qb'];
        if(!$oQb || !is_object($oQb))
        {
          //dump($poQB);
          $oQb = $this->_getModel()->getQueryBuilder();
          $oQb->addWhere(' (false) ');
          $asListMsg[] = ' Error, could not reload the search. ';
        }
      }

      //$poQB comes when doing a complex search
      if(empty($poQB))
      {
        $oQb = $this->_getModel()->getQueryBuilder();
        $oQb->setDataType(CONST_CANDIDATE_TYPE_COMP);

        require_once('component/sl_candidate/resources/search/quick_search.class.php5');
        $oQS = new CQuickSearch($oQb);
        $oQS->buildQuickSearch(CONST_CANDIDATE_TYPE_COMP);
      }
      else
        $oQb = $poQB;

      // ============================================
      // search management

      if(empty($this->csSearchId) && empty($nHistoryPk))
      {
        //$asListMsg[] = ' new search id [empty sId or history]. ';
        $this->csSearchId = manageSearchHistory($this->csUid, CONST_CANDIDATE_TYPE_COMP);
        $oQb->addLimit('0, 50');
        $nLimit = 50;
      }
      else
      {
        //$asListMsg[] = ' just apply pager to reloaded search. ';
        $oPager = CDependency::getComponentByName('pager');
        $oPager->initPager();
        $nLimit = $oPager->getLimit();
        $nPagerOffset = $oPager->getOffset();

        $oQb->addLimit(($nPagerOffset*$nLimit).' ,'. $nLimit);
      }

      $oQb->setTable('sl_company', 'scom');

      if ($poQB->hasSelect())
        $oQb->addSelect('GROUP_CONCAT(sind.label) as industry_list');
      else
        $oQb->addSelect('*, GROUP_CONCAT(sind.label) as industry_list');


      $oQb->addJoin('left', 'sl_attribute', 'satt', 'satt.type = \'cp_indus\' AND satt.itemfk = scom.sl_companypk');
      $oQb->addJoin('left', 'sl_industry', 'sind', 'sind.sl_industrypk = satt.attributefk');
      $oQb->addGroup('scom.sl_companypk');


      $sQuery = "SELECT *, GROUP_CONCAT(sind.label) as industry_list
          FROM sl_company as scom
          LEFT JOIN sl_attribute as satt ON (satt.type = 'cp_indus' AND satt.itemfk = scom.sl_companypk)
          LEFT JOIN sl_industry as sind ON (sind.sl_industrypk = satt.attributefk)
          GROUP BY scom.sl_companypk
          ORDER BY scom.sl_companypk DESC
          ";

      $sSortField = getValue('sortfield');
      $sSortOrder = getValue('sortorder', 'DESC');


      if(!empty($sSortField))
      {// calismiyor gibi...
        if ($sSortField == 'industry_list')
        {
          $oQb->addOrder("sind.label $sSortOrder");
        }
        else
        {
          ChromePhp::log('HERE');
          $oQb->addOrder("scom.$sSortField $sSortOrder");
        }
      }
      else
        $oQb->addOrder('scom.name DESC');

      //ChromePhp::log($oQb->getSql());
      $sql = $oQb->getSql();

      if(!empty($sSortField))
      {
        $sql = str_replace('ratio DESC ,','',$sql);

      }

      $sql = str_replace('AND  sind.label LIKE "%Industry%"','',$sql);

      $explodeLimit = explode('LIMIT',$sql);
      $noLimit = $explodeLimit[0];

      //ChromePhp::log($noLimit);
      $oDB = CDependency::getComponentByName('database');

      $db_result = $oDB->executeQuery($noLimit);

      $allResult = $db_result->getAll();
      $limitlessCount = count($allResult);

      // multi industries --> we need to group by companypk --> number result = numrows
      //$oDbResult = $this->_getModel()->executeQuery($oQb->getCountSql());

      $oDbResult = $this->_getModel()->executeQuery($sql);
      $bRead = $oDbResult->readFirst();
      if(!$bRead)
      {
        //return array('data' => $this->_oDisplay->getBlocMessage('no company found.'), 'sql' => $oQb->getSql(), 'action' => 'goPopup.removeLastByType(\'layer\');  ');
        return array('data' => $this->_oDisplay->getBlocMessage('no company found.'), 'sql' => $sql, 'action' => 'goPopup.removeLastByType(\'layer\');  ');
      }
      $nResult = $limitlessCount;
      //$nResult = (int)$oDbResult->getFieldValue('nCount');
      //$nResult = $oDbResult->numRows();
      if(empty($nResult))
        return array('data' => $this->_oDisplay->getBlocMessage('no company found for '.$oQb->getTitle()), 'nb_result' => $nResult, 'action' => 'goPopup.removeLastByType(\'layer\'); ');


      if(empty($nHistoryPk) /*&& !$bLogged*/)
      {
        $sURL = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_LIST, CONST_CANDIDATE_TYPE_COMP, 0, array('searchId' => $this->csSearchId));
        $sLink = 'javascript: loadAjaxInNewTab(\''.$sURL.'\', \'comp\', \'company\');';
        $nHistoryPk = logUserHistory($this->csUid, $this->csAction, $this->csType, $this->cnPk, array('text' => implode(', ', $asListMsg).' (#'.$nResult.' results)', 'link' => $sLink, 'data' => array('qb' => $oQb)), false);
      }

      //$oDbResult = $this->_getModel()->executeQuery($oQb->getSql());
      $oDbResult = $this->_getModel()->executeQuery($sql);
      $bRead = $oDbResult->readFirst();
      if(!$bRead)
      {
        //assert('false; // no company found in select, but count = '.$nResult.' ['.$oQb->getSql().']');
        assert('false; // no company found in select, but count = '.$nResult.' ['.$sql.']');
        //return array('data' => 'no company found.', 'sql' => $oQb->getSql());
        return array('data' => 'no company found.', 'sql' => $sql);
      }

      $asRow = array();

      while($bRead)
      {
        $nPk =  $oDbResult->getFieldValue('sl_companypk');
        $asRow[$nPk] = $oDbResult->getData();

        //$asRow[$nPk]['contact'] = $asRow[$nPk]['phone'].' - '.$asRow[$nPk]['fax'].' - '.$asRow[$nPk]['website'].' - '.$asRow[$nPk]['email'];
        $asRow[$nPk]['created_by'] = $oLogin->getUserLink((int)$asRow[$nPk]['created_by']);
        $bRead = $oDbResult->readNext();
      }

      $sListId = uniqid();
      $asParam = array('sub_template' => array('CTemplateList' => array(0 => array('row' => array('class' => 'CComp_row', 'path' => $_SERVER['DOCUMENT_ROOT'].self::getResourcePath().'template/comp_row.tpl.class.php5')))));
      $oTemplate = $this->_oDisplay->getTemplate('CTemplateList', $asParam);
      $oConf = $oTemplate->getTemplateConfig('CTemplateList');
      $oConf->setRenderingOption('full', 'full', 'full');

      $sActionContainerId = uniqid();
      $sPic = $this->_oDisplay->getPicture(self::getResourcePath().'/pictures/list_action.png');
      $sJavascript = "var oCurrentLi = $(this).closest('li');

        if($('> div.list_action_container', oCurrentLi).length)
        {
          $('> div.list_action_container', oCurrentLi).fadeToggle();
        }
        else
        {
          var oAction = $('#".$sActionContainerId."').clone().show(0);

          $(oCurrentLi).append('<div class=\'list_action_container hidden\'></div><div class=\'floatHack\' />');
          $('div.list_action_container', oCurrentLi).append(oAction).fadeIn();
        }";

      //Template related -- #2
      if($nResult <= $nLimit)
      {
        $sSortJs = 'javascript';
        $sURL = '';
        $nAjax = 0;
      }
      else
      {
        $sSortJs = '-';
        $sURL = $this->_oPage->getAjaxUrl('sl_candidate', $this->csAction, CONST_CANDIDATE_TYPE_COMP, 0, array('searchId' => $this->csSearchId, '__filtered' => 1, 'data_type' => CONST_CANDIDATE_TYPE_COMP, 'replay_search' => $nHistoryPk));
        $nAjax = 1;
      }

      $sActionLink = $this->_oDisplay->getLink($sPic, 'javascript:;', array('onclick' => $sJavascript));
      $oConf->addColumn($sActionLink, 'a', array('id' => 'aaaaaa', 'width' => '20'));
      $oConf->addColumn('ID', 'sl_companypk', array('width' => '43', 'sortable'=> array($sSortJs => 'text',
        'ajax' => $nAjax, 'url' => $sURL, 'ajax_target' => $this->csSearchId), 'style' => 'margin: 0;'));


      $oConf->addColumn('C', 'is_client', array('id' => '', 'width' => '20', 'sortable'=> array($sSortJs => 'value_integer',
        'ajax' => $nAjax, 'url' => $sURL, 'ajax_target' => $this->csSearchId)));
      $oConf->addColumn('NC', 'is_nc_ok', array('id' => '', 'width' => '20', 'sortable'=> array($sSortJs => 'value_integer',
        'ajax' => $nAjax, 'url' => $sURL, 'ajax_target' => $this->csSearchId)));
      $oConf->addColumn('L', 'level', array('id' => '', 'width' => '20', 'sortable'=> array($sSortJs => 'value_integer',
        'ajax' => $nAjax, 'url' => $sURL, 'ajax_target' => $this->csSearchId)));
      $oConf->addColumn('Company name', 'name', array('id' => '', 'width' => '31%', 'sortable'=> array($sSortJs => 'text',
        'ajax' => $nAjax, 'url' => $sURL, 'ajax_target' => $this->csSearchId)));
      $oConf->addColumn('Industry', 'industry_list', array('id' => '', 'width' => '18%', 'sortable'=> array($sSortJs => 'text',
        'ajax' => $nAjax, 'url' => $sURL, 'ajax_target' => $this->csSearchId)));
      $oConf->addColumn('Description', 'description', array('id' => '', 'width' => '22%', 'sortable'=> array($sSortJs => 'text',
        'ajax' => $nAjax, 'url' => $sURL, 'ajax_target' => $this->csSearchId)));
      //$oConf->addColumn('Contact', 'contact', array('id' => '', 'width' => '15%', 'sortable'=> array($sSortJs => 'text')));
      $oConf->addColumn('Created by', 'created_by', array('id' => '', 'width' => '10%', 'sortable'=> array($sSortJs => 'text',
        'ajax' => $nAjax, 'url' => $sURL, 'ajax_target' => $this->csSearchId)));

      $sTitle = $oQb->getTitle();
      if(!empty($sTitle))
        $asListMsg[] = $sTitle;

      $oConf->addBlocMessage('<span class="search_result_title_nb">'.$nResult.' result(s)</span> '.implode(', ', $asListMsg), array(), 'title');

      $oConf->setPagerTop(true, 'right', $nResult, $sURL.'&list=1', array('ajaxTarget' => '#'.$this->csSearchId));
      $oConf->setPagerBottom(true, 'right', $nResult, $sURL.'&list=1', array('ajaxTarget' => '#'.$this->csSearchId));

      //===========================================
      //===========================================
      //start building the HTML
      $sHTML = '';

      /* debug
       *
      if(!$bFilteredList)
        $sHTML.= $this->_oDisplay->getBlocStart($this->csSearchId, array('class' => 'scrollingContainer')).' new list';
      else
        $sHTML.= 'replay a search, pager offset '.$nPagerOffset.', container/search ID '.$this->csSearchId;*/

      if(!$bFilteredList)
        $sHTML.= $this->_oDisplay->getBlocStart($this->csSearchId, array('class' => 'scrollingContainer'));

      $sHTML.= $this->_oDisplay->getBlocStart($sActionContainerId, array('class' => 'hidden'));
/*        var_dump(
  $sHTML
  );
die();*/
      $sHTML.= '
        <div><input type="checkbox"
        onchange="if($(this).is(\':checked\')){ listSelectBox(\''.$sListId.'\', true); }else{ listSelectBox(\''.$sListId.'\', false); }"/>Select all</div>';

      $sURL = $this->_oPage->getAjaxUrl('sl_folder', CONST_ACTION_ADD, CONST_FOLDER_TYPE_FOLDER, 0, array('item_type' => CONST_CANDIDATE_TYPE_COMP));
      $sHTML.= '<div>Create a folder from [<a href="javascript:;" onclick="
        listBoxClicked($(\'#'.$sListId.' ul li:first\'));
        sIds = $(\'.multi_drag\').attr(\'data-ids\');
        if(!sIds)
          return alert(\'Nothing selected\');

        goPopup.setLayerFromAjax(\'\', \''.$sURL.'&ids=\'+sIds);">selected items</a>] OR';

      if($nResult <= 80000)
        $sHTML.= ' [<a href="javascript:;" onclick="goPopup.setLayerFromAjax(\'\', \''.$sURL.'&searchId='.$this->csSearchId.'\');">All '.$nResult.' results</a>]';
      else
        $sHTML.= ' [<span title="Too many results. Can\'t save more than 50000 results." style="font-style: italic">all</span> ]';

      $sURL = $this->_oPage->getAjaxUrl('sl_folder', CONST_ACTION_ADD, CONST_FOLDER_TYPE_ITEM, 0, array('item_type' => CONST_CANDIDATE_TYPE_COMP));
      $sHTML.= '</div><div>Move into a folder [<a href="javascript:;" onclick="
        listBoxClicked($(\'#'.$sListId.' ul li:first\'));
        sIds = $(\'.multi_drag\').attr(\'data-ids\');
        if(!sIds)
          return alert(\'Nothing selected\');

        goPopup.setLayerFromAjax(\'\', \''.$sURL.'&ids=\'+sIds);">selected ones</a>] OR';

      if($nResult <= 80000)
        $sHTML.= ' [<a href="javascript:;" onclick="goPopup.setLayerFromAjax(\'\', \''.$sURL.'&searchId='.$this->csSearchId.'\');">All '.$nResult.' results</a>]';
      else
        $sHTML.= ' [<span title="Too many results. Can\'t save more than 50000 results." style="font-style: italic">all</span> ]';

      $sHTML.= '</div>';

      if ($nResult > 1)
      {
        /*$sURL = $this->_oPage->getAjaxUrl('settings', CONST_ACTION_SAVEEDIT, CONST_TYPE_SAVED_SEARCHES, 0,
          array('action' => 'add', 'activity_id' => $nHistoryPk));

        $sHTML.= '<div><a href="javascript:;" onclick="ajaxLayer(\''.$sURL.'\', 370, 150);">Save this search</a></div>';*/
      }

      if(!empty($nFolderPk))
      {
        $sURL = $this->_oPage->getAjaxUrl('sl_folder', CONST_ACTION_DELETE, CONST_FOLDER_TYPE_ITEM, 0, array('folderpk' => $nFolderPk, 'item_type' => CONST_CANDIDATE_TYPE_COMP));
        $sHTML.= '<div>Remove from folder [<a href="javascript:;" onclick="listBoxClicked($(\'#'.$sListId.' ul li:first\'));
        sIds = $(\'.multi_drag\').attr(\'data-ids\');
        if(!sIds)
          return alert(\'Nothing selected\');

         AjaxRequest(\''.$sURL.'&ids=\'+sIds);
        ">selected</a>]
        [<a href="javascript:;" onclick="AjaxRequest(\''.$sURL.'&searchId='.$this->csSearchId.'\');">'.$nResult.' results</a>]</div>';
      }

      $sHTML.= $this->_oDisplay->getBlocEnd();

      //Add the list template to the html
      $sHTML.= $oTemplate->getDisplay($asRow, 1, 5, 'safdassda');


      //---------------------------------------------
      //manage javascript action
      $sURL = $this->_oPage->getAjaxUrl('sl_folder', CONST_ACTION_SAVEADD, CONST_FOLDER_TYPE_ITEM, 0);
      $sHTML.='<script> initDragAndDrop(\''.$sURL.'\'); </script>';

      if(count($asRow) == 1)
      {
        $asRow = current($asRow);
        $sURL = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_COMP, (int)$asRow['sl_companypk']);
        $sHTML.='<script> view_comp(\''.$sURL.'\'); </script>';
      }

      //DEBUG: Dropp the query at the end
      if($oLogin->getUserPk() == 367 || isDevelopment() )
      {
        $sHTML.= '<a href="javascript:;" onclick="$(this).parent().find(\'.query\').toggle(); ">query... </a>
          <span class="hidden query"><br />'.$sQuery.'</span><br /><br /><br />';
      }

      $sHTML .= '<script>
          $(function(){
            var list_container = document.getElementById(\''.$this->csSearchId.'\');
            $(\'.fixedListheader\').remove();
            list_container.scrollTop = 0;
          });
        </script>';

      if($gbNewSearch)
        $sHTML.= $this->_oDisplay->getBlocEnd();

      return array('data' => $sHTML, 'action' => ' initHeaderManager(); goPopup.removeLastByType(\'layer\'); ');
    }






    /* *********************************************************** */
    /* *********************************************************** */
    //save candidate form

    private function _saveCandidate($pnCandidatePk = 0)
    {
      //buffer to store all the data once checked, to be re-used for saving
      $this->casCandidateData = array();

      if(!empty($pnCandidatePk))
      {
        $asData = $this->_getModel()->getCandidateData($pnCandidatePk, true);
        if(empty($asData))
          return array('popupError' => 'Could not find the candidate you\'re trying to update. It may have been deleted.');


        if(!$this->_oLogin->isAdmin() && $asData['firstname'] != getValue('firstname'))
          return array('popupError' => 'Normal user cannot change candidate name');

        if(!$this->_oLogin->isAdmin() && $asData['keyword'] != '')
        {
          $oldKey = $asData['keyword'];
          $newKey = getValue('keyword');
          if (strpos($newKey, $oldKey) !== false)
          {
              #sikinti yok
          }
          else
          {
            return array('popupError' => 'Normal user cannot delete keyword');
          }
        }

        if(!$this->_oLogin->isAdmin() && $asData['lastname'] != getValue('lastname'))
          return array('popupError' => 'Normal user cannot change candidate name');

        //Date created is use and overwritten everywhere... so we're using an alias
        $asData['date_created'] = $asData['date_added'];
        $asData['sl_candidatepk'] = (int)$asData['sl_candidatepk'];
        $asData['sl_candidate_profilepk'] = (int)$asData['sl_candidate_profilepk'];
        $asData['created_by'] = (int)$asData['created_by'];
        $asData['is_birth_estimation'] = (int)$asData['is_birth_estimation'];

        $nProfilePk = $asData['sl_candidate_profilepk'];

        //security check
        if(!empty($pnCandidatePk) && empty($nProfilePk))
          assert('false; // we\'ve got a candidate without profile here ['.$pnCandidatePk.'].');

        if(empty($pnCandidatePk) || empty($nProfilePk))
          return array('popupError' => 'Could not find the candidate you\'re trying to update. It may have been deleted.');

        //for candi_profile table update
        $asData['candidatefk'] = $pnCandidatePk;
      }
      else
      {
        $asData = array();
      }


      //check ll the form fields (test mode only
      //dump('1st - saveCandiData ');
      $asError = $this->_saveCandidateData($pnCandidatePk, true, false, $asData);

      if(empty($pnCandidatePk))
      {
        //we re-use a function here, so the way it works and the returned value are a bit different
        //pass a dummy candipk here, will pass the real one when called to save
        $asResult = $this->_getCandidateContactSave(false, 999);
        if(isset($asResult['error']))
          $asError = array_merge($asError, (array)$asResult['error']);

        $asError = array_merge($asError, $this->_saveNotes(true, false, $asData));
        $asError = array_merge($asError, $this->_saveResume(true, false, $asData));

        $asError2 = array();
        $i = 0;
        foreach ($asError as $key => $value)
        {
          if($i == 0)
          {
            $asError2[] = $value;
            $i = 1;
          }
          else
          {
            if(!in_array($value, $asError2))
            {
              $asError2[] = $value;
            }
          }
        }

        $asError = $asError2;
      }

      // - - - - - - - - - - - - - - - - - - - - - - - -
      //All form sections have been checked.
      if(!empty($asError))
      {
        if(isset($this->casCandidateData['dup_tab']))
          return array('popupError' => implode("\n", $asError),  'data' =>  utf8_encode($this->casCandidateData['dup_tab']), 'action' => ' $(\'li.tab_duplicate\').show(0).click(); ');

        return array('popupError' => implode("\n", $asError));
      }





      //Now the form has been checked, we save... step by step again
      //dump('2nd - saveCandiData ');
      $asError = $this->_saveCandidateData($pnCandidatePk, false, true, $asData);
      if(!empty($asError))
        return array('popupError' => implode("    \n <br/>", $asError));

      if(!is_key($this->casCandidateData['profile']['candidatefk']))
        return array('popupError' => 'An error occured. Data may not have been saved.');


      if(empty($pnCandidatePk))
      {
        $asResult = $this->_getCandidateContactSave(true, $this->casCandidateData['profile']['candidatefk']);
        if(isset($asResult['error']))
          return array('popupError' => $asResult['error']);

        $asError = $this->_saveNotes(false, true, $this->casCandidateData['profile']);
        if(!empty($asError))
          return array('popupError' => implode("\n", $asError));

        $asError =  $this->_saveResume(false, true, $this->casCandidateData['profile']);
        if(!empty($asError))
          return array('popupError' => implode("\n", $asError));
      }


      //calculate quality ration and update profile table (update _in_play and _has_doc on the way)
      $this->updateCandidateProfile($this->casCandidateData['profile']['candidatefk']);

      $sURL = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_LIST, CONST_CANDIDATE_TYPE_CANDI, $this->casCandidateData['profile']['candidatefk']);
      $this->casCandidateData = array();

      if(empty($pnCandidatePk))
        return array('notice' => 'Candidate saved.', 'action' => '
          goPopup.removeLastByType(\'layer\');
          //view_candi(\''.$sURL.'\');
          var asContainer = goTabs.create(\'candi\', \'\',  \'\', \'Candidate list\');
          AjaxRequest(\''.$sURL.'\', \'body\', \'\',  asContainer[\'id\'], \'\', \'\', \'initHeaderManager(); \');
          goTabs.select(asContainer[\'number\']);
          ');

      return array('notice' => 'Candidate saved.', 'action' => '
        goPopup.removeLastByType(\'layer\');
        view_candi(\''.$sURL.'\'); ');
    }


    private function _saveCandidateData($pnCandidatePk = 0,$pbTest = true, $pbSave = false, $pasCandidate = array())
    {
      if(!assert('is_integer($pnCandidatePk)'))
        return array('error' => 'Bad parameters.');

      if(!assert('is_bool($pbTest) && is_bool($pbSave) && is_array($pasCandidate)'))
        return array('error' => 'Missing parameters.');

      $asError = array();

      if(empty($pnCandidatePk))
      {
        $nCandidatePk = $nProfilePk = 0;

        $asData = array();
        $asData['date_created'] = date('Y-m-d H:i:s');
        $asData['created_by'] = (int)$this->casUserData['loginpk'];
      }
      else
      {
        $nCandidatePk = $pasCandidate['sl_candidatepk'];
        $nProfilePk = $pasCandidate['sl_candidate_profilepk'];
        $candidate_information = getCandidateInformation($nCandidatePk);

        $asData = $pasCandidate;
        $asData['date_updated'] = date('Y-m-d H:i:s');
        $asData['updated_by'] = $this->casUserData['pk'];
      }

      if($pbTest)
      {
        //First form section
        $asData['sex'] = (int)getValue('sex');
        $asData['firstname'] = filter_var(getValue('firstname'), FILTER_SANITIZE_STRING);
        $asData['lastname'] = filter_var(getValue('lastname'), FILTER_SANITIZE_STRING);

        $asData['date_birth'] = trim(getValue('birth_date'));
        $nAge = (int)getValue('age', 0);
        if(!empty($nAge))
        {
          $asData['date_birth'] = date('Y', strtotime('-'.$nAge.' years')).'-02-02';
          $asData['is_birth_estimation'] = 1;
        }
        else
        {
          $asData['is_birth_estimation'] = 0;
        }


        $asData['languagefk'] = (int)getValue('language');
        $asData['nationalityfk'] = (int)getValue('nationality');
        $asData['locationfk'] = (int)getValue('location');

        $nNewCompanyFk = (int)getValue('companypk');
        $asData['companyfk'] = $nNewCompanyFk;
        $asData['occupationfk'] = (int)getValue('occupationpk');
        $asData['industryfk'] = (int)getValue('industrypk');

        if($pnCandidatePk > 0)
        {
          $dateNow = date('Y-m-d H:i:s');
          $sQuery = "UPDATE sl_candidate_old_companies SET flag = 'p' , last_activity = '".$dateNow."' WHERE candidate_id = '".$pnCandidatePk."'";

          $this->_getModel()->executeQuery($sQuery);

          $sQuery = "INSERT INTO sl_candidate_old_companies (candidate_id, company_id, first_activity, last_activity)
                     VALUES ('".$pnCandidatePk."','".$nNewCompanyFk."','".$dateNow."','".$dateNow."')";

          $this->_getModel()->executeQuery($sQuery);
        }

        $skillValues = array();
        $skillValues['skill_ag'] = getValue('skill_ag');
        $skillValues['skill_ap'] = getValue('skill_ap');
        $skillValues['skill_am'] = getValue('skill_am');
        $skillValues['skill_mp'] = getValue('skill_mp');
        $skillValues['skill_in'] = getValue('skill_in');
        $skillValues['skill_ex'] = getValue('skill_ex');
        $skillValues['skill_fx'] = getValue('skill_fx');
        $skillValues['skill_ch'] = getValue('skill_ch');
        $skillValues['skill_ed'] = getValue('skill_ed');
        $skillValues['skill_pl'] = getValue('skill_pl');
        $skillValues['skill_e'] = getValue('skill_e');

        foreach ($skillValues as $key => $skill)
        {
          if( !empty($skill) && $skill != '' && $skill != '-' && (!is_numeric($skill) || $skill < 1 || $skill > 9))
          {
            $asError[] = 'All skill areas should have a value between 1 - 9.';
          }
        }

        if(empty($asData['firstname']) || strlen($asData['firstname']) < 2)
          $asError[] = 'Firstname empty or too short.';

        if(empty($asData['lastname']) || strlen($asData['lastname']) < 2)
          $asError[] = 'Lastname empty or too short.';
        if(empty($asData['industryfk']))
          $asError[] = 'Industry is empty.';
        if(empty($asData['companyfk']))
          $asError[] = 'Company is empty.';
        if(empty($asData['occupationfk']))
          $asError[] = 'Occupation is empty.';

        if($pnCandidatePk == 0)
        {
          $sCharacter = getValue('character_note');
          $sNote = getValue('note');

          if(empty($sCharacter) && empty($sNote))
          {
            $asError[] = 'You have to input at least a note or a character note.';
          }
        }


        if($pnCandidatePk == 0)
        {
          $bEmpty = true;
          foreach($_POST['contact_value'] as $nRow => $sValue)
          {
            if(!empty($sValue) && $pnCandidatePk != 999)
            {
              $bEmpty = false;
              break;
            }
          }

          if($bEmpty)
          {
            $asError[] = 'No contact details (work,mobile or e-mail) input in the form.';
          }
        }


        if(empty($asData['date_birth']) || $asData['date_birth'] == '0000-00-00')
        {
          $asData['date_birth'] = 'NULL';
        }
        else
        {
          if(!is_date($asData['date_birth']) || $asData['date_birth'] < '1900-00-00')
          {
            $asError[] = 'Birth date invalid.';
          }
          $ageCalculate = DateTime::createFromFormat('Y-m-d', $asData['date_birth'])->diff(new DateTime('now'))->y;
          if($ageCalculate < 18)
          {
            $asError[] = "Age should be higher than 18";
          }
        }

        //Stops right here if firstname lastname are incorect
        if(!empty($asError))
          return $asError;

        if(!empty($pnCandidatePk) && $asData['companyfk'] != $nNewCompanyFk)
        {
          $asData['previous_company'] = (int)$asData['companyfk'];
          $asData['current_company'] = $nNewCompanyFk;
        }

        $asData['title'] = filter_var(getValue('title'), FILTER_SANITIZE_STRING);
        $asData['department'] = filter_var(getValue('department'), FILTER_SANITIZE_STRING);

        if(isset($_POST['client']))
          $asData['is_client'] = 1;
        else
          $asData['is_client'] = 0;

        if(empty($pnCandidatePk))
        {
          $asData['_sys_status'] = 0;
          $asData['_sys_redirect'] = null;
        }
        else
        {
          $asData['_sys_status'] = (int)getValue('_sys_status', 0);
          $asData['_sys_redirect'] = (int)getValue('_sys_redirect', 0);
          if(empty($asData['_sys_redirect']))
            $asData['_sys_redirect'] = NULL;
        }


        if(empty($asData['industryfk']))
          $asError[] = 'Industry field is required.';

        if(empty($asData['occupationfk']))
          $asError[] = 'Occupation field is required.';

        if(empty($asData['companyfk']) || !is_key($asData['companyfk']))
          $asError[] = 'Company field is required.';

        if(!empty($asData['title']) && strlen($asData['title']) < 3)
          $asError[] = 'Title must contains at least 3 characters';

        if(!empty($asData['department']) && strlen($asData['department']) < 2)
          $asError[] = 'Department must contains at least 2 characters';


        //---------------------------------------------------------------------------------
        //Salary section
        //Check the field content to look for currency and Multiplier
        $oForm = CDependency::getComponentByName('form');
        $oCurrency = $oForm->getStandaloneField('currency');

        $asSalary = $oCurrency->getCurrencyFromPost('salary');
        $this->_getSalaryInYen($asSalary);

        $asBonus = $oCurrency->getCurrencyFromPost('bonus');
        $this->_getSalaryInYen($asBonus);

        if(!empty($asSalary['value']) && ($asSalary['yen'] > 100000000 || $asSalary['yen'] < 10000))
          $asError[] = 'Salary value is not a valid number. ['.$asSalary['yen'].' '.$asSalary['currency'].']';

        if(!empty($asBonus['value']) && ($asBonus['yen'] > 100000000 || $asBonus['yen'] < 10000))
         $asError[] = 'Bonus value is not a valid number. ['.$asBonus['yen'].' '.$asBonus['currency'].']';

        $asData['salary'] = $asSalary['value'];
        $asData['currency'] = $asSalary['currency'];
        $asData['currency_rate'] = $asSalary['rate'];
        $asData['bonus'] = $asBonus['value'];
        $asData['salary_search'] = (int)($asSalary['yen'] + $asBonus['yen']);

        $asTargetLow = $oCurrency->getCurrencyFromPost('target_low');

        $testTargetSalary = (int)getValue('target_low');

        //ChromePhp::log($asTargetLow['yen']);

        $this->_getSalaryInYen($asTargetLow);

        $asTargetHigh = $oCurrency->getCurrencyFromPost('target_high');
        $this->_getSalaryInYen($asTargetHigh);

        if(!empty($asTargetLow['value']) && ($asTargetLow['yen'] > 100000000 || $asTargetLow['yen'] < 10000))
          $asError[] = 'Target salary low value is not a valid number. ['.$asTargetLow['yen'].' '.$asTargetLow['currency'].']';

        if(!empty($asTargetHigh['value']) && ($asTargetHigh['yen'] > 100000000 || $asTargetHigh['yen'] < 10000))
          $asError[] = 'Target salary high value is not a valid number. ['.$asTargetHigh['yen'].' '.$asTargetHigh['currency'].']';

        $asData['target_low'] = $asTargetLow['value'];
        $asData['target_high'] = $asTargetHigh['value'];
        //---------------------------------------------------------------------------------


         //third form section
        $asData['grade'] = (int)getValue('grade');
        $asData['statusfk'] = (int)getValue('status');
        //extra test & actions here

        if($asData['statusfk'] >= 4)
        {
          //Assessed candidate needs a character note
          if(empty($pnCandidatePk) && !getValue('character_note'))
          {
            $asError[] = 'Character note is required for any assessed candidate.';
          }
          elseif(!empty($pnCandidatePk))
          {
            $oNote = CDependency::getComponentByName('sl_event');
            $asNote = $oNote->getNotes($pnCandidatePk, CONST_CANDIDATE_TYPE_CANDI, 'character');
            $newCharacterNotes = getSlNotes($pnCandidatePk);

            if(empty($asNote) && empty($newCharacterNotes))
            {
              //index.php5?uid=555-004&ppa=ppaa&ppt=event&ppk=0&cp_uid=555-001&cp_action=ppav&cp_type=candi&cp_pk=400006&default_type=note&pg=ajx
              $asItem = array('cp_uid' => '555-001', 'cp_action' => CONST_ACTION_VIEW, 'cp_type' => CONST_CANDIDATE_TYPE_CANDI, 'cp_pk' => $pnCandidatePk, 'default_type' =>'character', 'no_candi_refresh' => 1);
              $sURL = $this->_oPage->getAjaxUrl('555-004', CONST_ACTION_ADD, CONST_EVENT_TYPE_EVENT, 0, $asItem);
              $asError[] = 'Character note is required for any assessed candidate.<br />
                Add a <a href="javascript:;" style="color: red;" onclick="goPopup.removeActive(\'message\'); var oConf = goPopup.getConfig(); oConf.width = 950; oConf.height = 550; goPopup.setLayerFromAjax(oConf, \''.$sURL.'\');" >character note now</a> or change back the candidate status. ';
            }
          }
        }

        $sDiploma = getValue('diploma');
        $asData['cpa'] = $asData['mba'] = 0;
        if($sDiploma == 'cpa' || $sDiploma == 'both')
          $asData['cpa'] = 1;

        if($sDiploma == 'mba' || $sDiploma == 'both')
          $asData['mba'] = 1;

        $asData['keyword'] = filter_var(getValue('keyword'), FILTER_SANITIZE_STRING);
        $asData['play_for'] = (int)getValue('play_for');
        $asData['play_date'] = null;

        $asData['is_client'] = getValue('client');
        if(empty($asData['is_client']))
          $asData['is_client'] = 0;
        else
          $asData['is_client'] = 1;

        $asData['skill_ag'] = (int)getValue('skill_ag', 0);
        $asData['skill_ap'] = (int)getValue('skill_ap', 0);
        $asData['skill_am'] = (int)getValue('skill_am', 0);
        $asData['skill_mp'] = (int)getValue('skill_mp', 0);
        $asData['skill_in'] = (int)getValue('skill_in', 0);
        $asData['skill_ex'] = (int)getValue('skill_ex', 0);
        $asData['skill_fx'] = (int)getValue('skill_fx', 0);
        $asData['skill_ch'] = (int)getValue('skill_ch', 0);
        $asData['skill_ed'] = (int)getValue('skill_ed', 0);
        $asData['skill_pl'] = (int)getValue('skill_pl', 0);
        $asData['skill_e'] = (int)getValue('skill_e', 0);

        //convert 0 to null
        $asData['skill_ag'] = ((empty($asData['skill_ag']))? 'null': $asData['skill_ag']);
        $asData['skill_ap'] = ((empty($asData['skill_ap']))? 'null': $asData['skill_ap']);
        $asData['skill_am'] = ((empty($asData['skill_am']))? 'null': $asData['skill_am']);
        $asData['skill_mp'] = ((empty($asData['skill_mp']))? 'null': $asData['skill_mp']);
        $asData['skill_in'] = ((empty($asData['skill_in']))? 'null': $asData['skill_in']);
        $asData['skill_ex'] = ((empty($asData['skill_ex']))? 'null': $asData['skill_ex']);
        $asData['skill_fx'] = ((empty($asData['skill_fx']))? 'null': $asData['skill_fx']);
        $asData['skill_ch'] = ((empty($asData['skill_ch']))? 'null': $asData['skill_ch']);
        $asData['skill_ed'] = ((empty($asData['skill_ed']))? 'null': $asData['skill_ed']);
        $asData['skill_pl'] = ((empty($asData['skill_pl']))? 'null': $asData['skill_pl']);
        $asData['skill_e'] =  ((empty($asData['skill_e']))? 'null': $asData['skill_e']);


        //save all the profile data
        $this->casCandidateData['profile'] = $asData;

        $asData['contact'] = getValue('contact_value');

        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        // check duplicates when creating new candidate
        if(empty($pasCandidate))
        {
          $sDuplicate = getValue('check_duplicate');
          if(empty($sDuplicate) || $sDuplicate != $asData['lastname'].'_'.$asData['firstname'])
          {
            $sDuplicate = $this->_checkDuplicate($asData);
            if(!empty($sDuplicate))
            {
              $asError[] = 'There may be duplicates. Please check the duplicate tab.';
              $this->casCandidateData['dup_tab'] = $sDuplicate;
            }
          }
        }
      }

      if(!empty($asError))
      {
        $this->casCandidateData['profile'] = array(); //don't save profiles with errors
        return $asError;
      }


      if($pbSave)
      {
        //dump($this->casCandidateData['profile']);
        if(empty($nCandidatePk))
        {
          $bNewCandidate = true;
          $nKey = $this->_getModel()->add($this->casCandidateData['profile'], 'sl_candidate');
          if(!$nKey)
          {
            assert('false; // Could not add the candidate.');
            return array('error' => __LINE__.' - An error occurred. Could not add the candidate.');
          }

          if(empty($asData['locationfk']))
            $sLocation = 'TOK';
          else
          {
            $asLocation = $this->getVars()->getLocationList();
            $sLocation = $asLocation[$asData['locationfk']];
          }

          $sUid = sprintf("%'#4s", substr($this->casUserData['id'], 0, 4));
          $sUid.= sprintf("%'03s", substr($sLocation, 0, 3));
          $sUid.= date('y') . chr( (64+date('m')) ) . $nKey;

          $this->casCandidateData['profile']['candidatefk'] = $nKey;
          $this->casCandidateData['profile']['uid'] = strtoupper($sUid);
        }
        else
        {
          $bNewCandidate = false;

          //dump($this->casCandidateData['profile']);
          $bQuery = $this->_getModel()->update($this->casCandidateData['profile'], 'sl_candidate', 'sl_candidatepk = '.$nCandidatePk);
          if(!$bQuery)
          {
            assert('false; // Could not update the candidate.');
            return array('error' => __LINE__.' - An error occurred. Could not add the candidate.');
          }
        }

        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        //candidate table added or update... deal with the business profile
        if($bNewCandidate)
        {
          //$asMonth = array('A','B','C','D','E','F','G','H','I','J','K','L');
          //$this->casCandidateData['profile']['uid'] = substr($this->casUserData['id'], 0, 4);
          //$this->casCandidateData['profile']['uid'].= 'LOC'.date('y').$asMonth[(int)date('m')].$this->casCandidateData['profile']['candidatefk'];

          $bSaved = (bool)$this->_getModel()->add($this->casCandidateData['profile'], 'sl_candidate_profile');

          $candidate_info = getCandidateInformation($nKey);
          $company_id = $candidate_info['companyfk'];
          $this->_addOldCompany($nKey,$company_id);

        }
        else
        {
          $this->casCandidateData['profile']['date_updated'] = date('Y-m-d H:i:s');
          $this->casCandidateData['profile']['updated_by'] = (int)$this->casUserData['loginpk'];

          $bSaved = $this->_getModel()->update($this->casCandidateData['profile'], 'sl_candidate_profile', 'sl_candidate_profilepk = '.$nProfilePk);

          if(isset($this->casCandidateData['profile']['previous_company']))
          {
            //need to log the company changing
            $oNote = CDependency::getComponentByName('sl_event');

            $nCompany = (int)$this->casCandidateData['profile']['previous_company'];
            $asCompany = $this->_getModel()->getCompanyData($nCompany);
            $sFrom = $asCompany['name'];
            $sNote = 'Candidate has been updated. Company changed from [ #'.$nCompany.' - '.$sFrom.'] ';

            $nCompany = $this->casCandidateData['profile']['companyfk'];
            $asCompany = $this->_getModel()->getCompanyData($nCompany);
            $sNote.= 'to [ #'.$nCompany.' - '.$asCompany['name'].' ]<br />';

            //add a note from  system user
            $oNote->addNote($nCandidatePk, 'cp_history', $sNote, (int)$this->casUserData['pk']);
            $oNote->addNote($nCandidatePk, 'cp_hidden', $sFrom, (int)$this->casUserData['pk']);
          }
        }


        if(!$bSaved)
        {
          assert('false; // Could not save the candidate profile.');
          return array('error' => __LINE__.' - An error occurred. Could not save the candidate data.');
        }

        //A candidate has been updated... we create a detailed log entry
        if(!empty($pasCandidate))
        {
          $this->_customLogUpdate($pasCandidate, $this->casCandidateData['profile']);
          //$pasCandidate  VS $this->casCandidateData['profile']
        }

        //-------------------------------------------------------------------------------------
        //-------------------------------------------------------------------------------------
        // candidate saved/updated... i can now manage the linked attributes

        $asAllAttribute = array();
        $sNow = date('Y-m-d H:i:s');

        $sAltOccupation = getValue('alt_occupationpk');
        if(!empty($sAltOccupation))
        {
          $asAttribute = explode(',', $sAltOccupation);
          foreach($asAttribute as $sAttributeFk)
          {
            $asAllAttribute['type'][] = 'candi_occu';
            $asAllAttribute['itemfk'][] = $this->casCandidateData['profile']['candidatefk'];
            $asAllAttribute['attributefk'][] = (int)$sAttributeFk;
            $asAllAttribute['loginfk'][] = $this->casUserData['pk'];
            $asAllAttribute['date_created'][] = $sNow;
          }
        }

        $sAltIndustry = getValue('alt_industrypk');
        if(!empty($sAltIndustry))
        {
          $asAttribute = explode(',', $sAltIndustry);
          foreach($asAttribute as $sAttributeFk)
          {
            $asAllAttribute['type'][] = 'candi_indus';
            $asAllAttribute['itemfk'][] = $this->casCandidateData['profile']['candidatefk'];
            $asAllAttribute['attributefk'][] = (int)$sAttributeFk;
            $asAllAttribute['loginfk'][] = $this->casUserData['pk'];
            $asAllAttribute['date_created'][] = $sNow;
          }
        }

        $asAttribute = @$_POST['alt_language'];
        if(!empty($asAttribute))
        {
          foreach($asAttribute as $sAttributeFk)
          {
            $asAllAttribute['type'][] = 'candi_lang';
            $asAllAttribute['itemfk'][] = $this->casCandidateData['profile']['candidatefk'];
            $asAllAttribute['attributefk'][] = (int)$sAttributeFk;
            $asAllAttribute['loginfk'][] = $this->casUserData['pk'];
            $asAllAttribute['date_created'][] = $sNow;
          }
        }

        if(!empty($asAllAttribute))
        {
          if(!$bNewCandidate)
            $this->_getModel()->deleteByWhere('sl_attribute', '`type` IN ("candi_occu", "candi_indus", "candi_lang") AND itemfk='.$this->casCandidateData['profile']['candidatefk']);

          $nInserted = $this->_getModel()->add($asAllAttribute, 'sl_attribute');
          if(empty($nInserted))
            return array('error' => 'Could not save the alternative data.');
        }

      }

      return $asError;
    }

// candidate duplica control starts
    private function _checkDuplicate($candidate_info)
    {

      $duplicate_array = $this->_getModel()->getDuplicate($candidate_info);

      if(empty($duplicate_array['company']) && empty($duplicate_array['other']))
        return '';

      $html = $this->_oDisplay->getCR();

      foreach ($duplicate_array['company'] as $key => $value)
      {
        $url = $this->_oPage->getAjaxUrl('555-001', CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI,
          (int)$value['sl_candidatepk']);
        $duplicate_array['company'][$key]['candidate'] = '<a href="javascript:;" onclick="popup_candi(this, \''.$url.'\');">';
        $duplicate_array['company'][$key]['candidate'] .= $value['lastname'].' '.$value['firstname'].'</a>';

        $duplicate_array['company'][$key]['ratio'] = number_format($value['ratio'], 1).'%';
      }

      foreach ($duplicate_array['other'] as $key => $value)
      {
        $url = $this->_oPage->getAjaxUrl('555-001', CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI,
          (int)$value['sl_candidatepk']);
        $duplicate_array['other'][$key]['candidate'] = '<a href="javascript:;" onclick="popup_candi(this, \''.$url.'\');">';
        $duplicate_array['other'][$key]['candidate'] .= $value['lastname'].' '.$value['firstname'].'</a>';

        $duplicate_array['other'][$key]['ratio'] = number_format($value['ratio'], 1).'%';
      }

      if (!empty($duplicate_array['company']))
      {
        $other_duplicates_visibility = 'style="display: none;"';
        $company_duplicates_visibility = '';
      }
      else
      {
        $other_duplicates_visibility = '';
        $company_duplicates_visibility = 'style="display: none;"';
      }

      $params = array('sub_template' => array('CTemplateList' => array(0 => array('row' => 'CTemplateRow'))));
      $template_obj = $this->_oDisplay->getTemplate('CTemplateList', $params);

      //get the config object for a specific template (contains default value so it works without config)
      $template = $template_obj->getTemplateConfig('CTemplateList');
      $template->setRenderingOption('full', 'full', 'full');
      $template->setPagerTop(false);
      $template->setPagerBottom(false);

      $template->addColumn('Matching', 'ratio', array('width' => 65, 'sortable'=> array('javascript' => 1)));
      $template->addColumn('refId', 'sl_candidatepk', array('width' => 50, 'sortable'=> array('javascript' => 1)));
      $template->addColumn('Candidate', 'candidate', array('width' => 210, 'sortable'=> array('javascript' => 1)));
      $template->addColumn('Company', 'company', array('width' => 250, 'sortable'=> array('javascript' => 1)));
      $template->addColumn('Industry', 'industry', array('width' => 150, 'sortable'=> array('javascript' => 1)));
      $template->addColumn('Occupation', 'occupation', array('width' => 150, 'sortable'=> array('javascript' => 1)));

      $html .= '<div '.$company_duplicates_visibility.'>'.'<div class="general_form_row">Duplicates in same company</div>';

      $html .= $template_obj->getDisplay($duplicate_array['company']).'</div>';

      $html .= '
        <div id="other_duplicates_button" class="general_form_row add_margin_top_10 fake_link">
          Duplicates in other companies
        </div>';

      $html .= '<div id="other_duplicates" '.$other_duplicates_visibility.'>'.$template_obj->getDisplay($duplicate_array['other']).'</div>';

      $duplicate_name = $candidate_info['lastname'].'_'.$candidate_info['firstname'];

      $html.= $this->_oDisplay->getCR(2);
      $link = '>>&nbsp;&nbsp;&nbsp;&nbsp;Click here if none of the above is a duplicate !&nbsp;&nbsp;&nbsp;&nbsp;<< &nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="Not a duplicate"/>';

      $html.= $this->_oDisplay->getLink($link, 'javascript:;', array( 'style' => 'font-weight: bold; color: #CC7161; font-size: 14px; ',
          'onclick' => '$(\'#dup_checked\').val(\''.$duplicate_name.'\'); $(\'.tab_duplicate\').hide(); $(\'.candidate_form_tabs li:first\').click();'));

      $html .= '
        <script>
          $("#other_duplicates_button").click(function()
          {
            $("#other_duplicates").toggle();
          });
        </script>';

      return $html;
    }
// candidate duplica control ends



    /* a pickle ?
     * private function _saveContactDetails($pbTest = true, $pbSave = false, $pasCandidate = array())
    {
      $asError = array();
      if($pbTest)
      {
        $asRowChecked = array();

        for($nCount = 0; $nCount < 4; $nCount++)
        {
          $sType = (int)$_POST['contact_type'][$nCount];
          $sValue = $_POST['contact_value'][$nCount];
          $nVisibility = (int)$_POST['contact_visibility'][$nCount];
          $sDescription = $_POST['contact_description'][$nCount];
          $asUser = $_POST['userfk'][$nCount];
          $bRowOk = true;

          if(!empty($sValue))
          {
            if(empty($sType) || empty($nVisibility))
            {
              $asError[] = 'row #'.($nCount++).': type or visibility invalid.';
              $bRowOk = false;
            }

            if($nVisibility == 4 && empty($asUser))
            {
              $asError[] = 'row #'.($nCount++).': if visibility is set on custom, you need to select the user who can access the contact data.';
              $bRowOk = false;
            }

            if($bRowOk)
              $asRowChecked[] = array('type' => $sType, 'value' => $sValue, 'description' => $sDescription, 'visibility' => $nVisibility, 'users' => $asUser);
          }
        }

        $this->casCandidateData['contact'] = $asRowChecked;
      }

      if(!empty($asError))
        return $asError;

      if($pbSave)
      {
        //$this->_
      }
      return $asError;
    }*/

  private function _addOldCompany($candidate_id,$company_id)
  {

    $oDB = CDependency::getComponentByName('database');

    $dateNow = date('Y-m-d H:i:s');

    $sQuery = "INSERT INTO sl_candidate_old_companies (candidate_id, company_id, first_activity, last_activity)
               VALUES ('".$candidate_id."','".$company_id."','".$dateNow."','".$dateNow."')";

    $oDB->executeQuery($sQuery);

  }

    private function _saveNotes($pbTest = true, $pbSave = false, $pasCandidate = array())
    {
      $asError = array();

      $sCharacter = getValue('character_note');
      $sNote = getValue('note');

      /*$personality_note = getValue('personality_note');
      $current_podition_note = getValue('current_podition_note');
      $product_exp_note = getValue('product_exp_note');
      $compensation_note = getValue('compensation_note');
      $move_note = getValue('move_note');
      $career_note = getValue('career_note');
      $timeline_note = getValue('timeline_note');
      $keywants_note = getValue('keywants_note');
      $past_note = getValue('past_note');
      $education_note = getValue('education_note');

      $allAreas = array();
      $allAreas['Personality_and_communication'] = $personality_note;
      $allAreas['Current_position_and_responsibilities'] = $current_podition_note;
      $allAreas['Product_or_technical_expertise'] = $product_exp_note;
      $allAreas['Compensation_breakdown'] = $compensation_note;
      $allAreas['Reason_for_moving'] = $move_note;
      $allAreas['Information_on_earlier_career'] = $career_note;
      $allAreas['Move_timeline'] = $timeline_note;
      $allAreas['Key_wants'] = $keywants_note;
      $allAreas['Companies_introduced_within_past_6–12_months'] = $past_note;
      $allAreas['Education_–_higher_educations'] = $education_note;*/

      //$candidate_id = $pasCandidate['candidatefk'];// daha once tamamlanmis meeting varmi yok mu bakalim.
      //varsa 8 alanin doldurulmasi yetecek yoksa 10 ve her alanda 20 karakter olmak zorunda...

      //$completedMeetings = getCompletedMeetings($candidate_id);
      //ChromePhp::log($completedMeetings);
      // bu kisimda aday zaten ilk dea ekleniyor o nedenle hepsi doldurulacak ve minimum 25 character olacak.

      /*$pnL = strlen($personality_note);
      $cpL = strlen($current_podition_note);
      $peL = strlen($product_exp_note);
      $cnL = strlen($compensation_note);
      $mnL = strlen($move_note);
      $caL = strlen($career_note);
      $tnL = strlen($timeline_note);
      $knL = strlen($keywants_note);
      $paL = strlen($past_note);
      $enL = strlen($education_note);

      $noteFlag1 = false;
      $noteflag2 = false;

      if(empty($personality_note) && empty($current_podition_note) && empty($product_exp_note) && empty($compensation_note) && empty($move_note) && empty($career_note) && empty($timeline_note) && empty($keywants_note) && empty($past_note) && empty($education_note) && empty($sNote))
      {
        $asError[] = 'You have to input at least a note or a character note.';
      }*/


      if(empty($sCharacter) && empty($sNote))
      {
        $asError[] = 'You have to input at least a note or a character note.';
      }

      if(!empty($asError))
        return $asError;

      $characterNoteFlag = false;
      $characterNote = "";
      if($pbSave)
      {
        $oEvent = CDependency::getComponentByName('sl_event');

        /*foreach ($allAreas as $key => $value)
        {
          if(!empty($value))
          {
            $characterNoteFlag  = true;
            $title = str_replace('_',' ',$key);
            $title .= " :";
            $characterNote .= "<b>".$title."</b>" .$value."<br>";
          }
        }
        if($characterNoteFlag)
        {
          $asResult = $oEvent->addNote((int)$pasCandidate['candidatefk'], 'character_note', $characterNote);
        }*/

        if(!empty($sCharacter))
        {
          $asResult = $oEvent->addNote((int)$pasCandidate['candidatefk'], 'character', $sCharacter);
          if(isset($asResult['error']))
            return $asResult;
        }

        if(!empty($sNote))
        {
          $asResult = $oEvent->addNote((int)$pasCandidate['candidatefk'], 'note', $sNote);
          if(isset($asResult['error']))
            return $asResult;
        }
      }

      return $asError;
    }

    private function _saveResume($pbTest = true, $pbSave = false, $pasCandidate = array())
    {
      $asError = array();

      $desc = getValue('doc_description');
      if($pbSave && isset($desc) && !empty($desc) && isset($pasCandidate['candidatefk']) && !empty($pasCandidate['candidatefk']))
      {
        $desc = getValue('doc_description');
        $passResume = $desc;


        $array = array($pasCandidate['candidatefk'],$desc);

        $this->_getResumeSaveAdd($array);
        $passResume = '';
      }

      else
      {
        if(empty($_FILES) || empty($_FILES['document']['name']))
        {
          $asError[] = 'No file selected.';
          return array();
        }

        if($pbTest)
        {
          if(empty($_FILES['document']['tmp_name']))
            $asError[] = 'No resume uploaded. It could be a transfer error, or you\'ve forgotten to select a file.';
        }

        if(!empty($asError))
          return array('error' => implode('<br />', $asError));

        if($pbSave)
        {
          $sTitle = getValue('doc_title');
          $sDescription = getValue('doc_description');

          if(empty($sTitle))
            $sTitle = $pasCandidate['lastname'].'_'.$pasCandidate['firstname'].'_resume';

          $sTitle = str_replace(' ', '_', $sTitle);

          $oSharedspace = CDependency::getComponentByName('sharedspace');
          $asItemLink = array(CONST_CP_UID => '555-001', CONST_CP_ACTION => CONST_ACTION_VIEW, CONST_CP_TYPE => CONST_CANDIDATE_TYPE_CANDI, CONST_CP_PK => $pasCandidate['candidatefk']);
          $asResult = $oSharedspace->quickAddDocument($asItemLink, $sTitle, $sDescription, 0, 'resume');

          if(isset($asResult['error']))
            $asError[] = $asResult['error'];
        }
      }

      return $asError;
    }




    private function _getSalaryInYen(&$pasSalaryData)
    {
      //dump($pasSalaryData);
      if(!assert('is_array($pasSalaryData) && !empty($pasSalaryData)'))
        return -1;

      if(!isset($pasSalaryData['value']) || !isset($pasSalaryData['currency']))
      {
        assert('false; // invalid salary data ');
        return -1;
      }

      $pasSalaryData['yen'] = 0;
      $pasSalaryData['rate'] = 1;

      if(empty($pasSalaryData['value']))
        return 0;

      if(empty($pasSalaryData['currency']) == 'jpy')
        return 0;

      //convert the value in yen
      $asCurrencyRate = $this->getVars()->getCurrencies();

      if(!isset($asCurrencyRate[$pasSalaryData['currency']]))
        return -1;

      $fRate = (float)$asCurrencyRate[$pasSalaryData['currency']];
      $pasSalaryData['rate'] = $fRate;
      $pasSalaryData['yen'] = $this->_roundSalary((int)$pasSalaryData['value'] / $fRate);

      /*dump('currency');
      dump($pasSalaryData['currency']);
      dump('currency rate');
      dump($fRate);
      dump('calculated value:  '.$fRate.' / '.$pasSalaryData['value']);
      dump($pasSalaryData['yen']);*/

      return 1;
    }

    private function _roundSalary($pvNumber, $pnPrecision = 1)
    {
      if(!assert('is_integer($pvNumber) || is_float($pvNumber)'))
        return 0;

      if(!assert('is_integer($pnPrecision)'))
        return 0;

      $nDivisor = pow(10, $pnPrecision);
      return round($pvNumber/$nDivisor) * $nDivisor;
    }




    public function updateCandidateProfiles()
    {
      $nLimit = (int)getValue('limit', 0);
      if(!empty($nLimit))
        $sLimit = '1, '.$nLimit;
      else
        $sLimit = '1, 250';

      $oDbResult = $this->_getModel()->getByWhere('sl_candidate_profile', '1', 'candidatefk', '_date_updated, candidatefk DESC', $sLimit);
      $bRead = $oDbResult->readFirst();
      $fStart = microtime(true);

      $nCount = 0;
      while($bRead)
      {
        $nCandidate = (int)$oDbResult->getFieldValue('candidatefk');
        $this->updateCandidateProfile($nCandidate);
        //echo 'candidate '.$nCandidate.' updated<br />';
        usleep(100);

        if($nCount == 0)
          echo ' Starts with candidate '.$nCandidate.'<br />';

        if(($nCount%500) == 0)
        {
          echo $nCount.' candidates updated<br />';
          flush();
          ob_flush();
        }

        $bRead = $oDbResult->readNext();
        $nCount++;
      }
      echo ' Ends with candidate '.$nCandidate.'<br />';

      $fStop = microtime(true);
      dump('Took '.round((($fStop-$fStart)), 3).'s to treat candidates '.$sLimit.' <br />');
      return true;
    }

    public function calculate_profile_rating($pnCandidatePk)
    {
      if(!assert('is_key($pnCandidatePk)'))
        return array();

      $asData = $this->_getModel()->getCandidateData($pnCandidatePk, true);
      if(empty($asData))
        return array();

      $nScore = 0;
      if(!empty($asData['languagefk']))
        $nScore+= 3;

      if(!empty($asData['nationalityfk']))
        $nScore+= 3;

      if(!empty($asData['locationfk']))
        $nScore+= 3;

      if(!empty($asData['date_birth']))
      {
        if($asData['is_birth_estimation'])
          $nScore+= 3;
        else
          $nScore+= 5;
      }


      if($asData['cpa'] != null || $asData['mba'] != null)
        $nScore+= 3;

      if(!empty($asData['skill_ag']))
        $nScore+= 3;
      if(!empty($asData['skill_ap']))
        $nScore+= 3;
      if(!empty($asData['skill_am']))
        $nScore+= 3;
      if(!empty($asData['skill_mp']))
        $nScore+= 3;
      if(!empty($asData['skill_in']))
        $nScore+= 3;
      if(!empty($asData['skill_ex']))
        $nScore+= 3;
      if(!empty($asData['skill_fx']))
        $nScore+= 3;
      if(!empty($asData['skill_ch']))
        $nScore+= 3;
      if(!empty($asData['skill_ed']))
        $nScore+= 3;
      if(!empty($asData['skill_pl']))
        $nScore+= 3;
      if(!empty($asData['skill_e']))
        $nScore+= 3;

      if(!empty($asData['title']))
        $nScore+= 5;

      if(!empty($asData['department']))
        $nScore+= 5;

      if(!empty($asData['keyword']))
        $nScore+= 3;
      if(!empty($asData['salary']))
        $nScore+= 5;
      if(!empty($asData['bonus']))
        $nScore+= 5;
      if(!empty($asData['target_low']))
        $nScore+= 3;
      if(!empty($asData['target_high']))
        $nScore+= 3;


      //Update _has_doc and used for quality ratio
      $asItem = array(CONST_CP_UID => $this->csUid, CONST_CP_ACTION => CONST_ACTION_VIEW, CONST_CP_TYPE => CONST_CANDIDATE_TYPE_CANDI, CONST_CP_PK => $pnCandidatePk);
      $oShareSpace = CDependency::getComponentByName('sharedspace');
      $asDocument = $oShareSpace->getDocuments(-1, $asItem);
      //dump($asDocument);
      $nDocument = (int)!empty($asDocument);
      if($nDocument == 0)
        $nScore+= -10;
      else
        $nScore+= 10;

      //calculating ratio
      $oNote = CDependency::getComponentByName('sl_event');
      $asCharNote = $oNote->getNotes($pnCandidatePk, CONST_CANDIDATE_TYPE_CANDI, 'character');
      //dump($asCharNote);
      $asNote = $oNote->getNotes($pnCandidatePk, CONST_CANDIDATE_TYPE_CANDI, '', array('character'));
      //dump($asNote);

      $nCharNote = count($asCharNote);
      if($nCharNote > 3)
        $nCharNote = 3;

      $nNote = count($asNote);
      if($nNote > 3)
        $nNote = 3;

      if(empty($nCharNote) && empty($nNote))
        $nScore-= 15;
      else
        $nScore+= ($nCharNote * 7) + ($nNote * 7);


      $sWhere = ' item_type = "'.CONST_CANDIDATE_TYPE_CANDI.'" AND itemfk = "'.$pnCandidatePk.'" ';
      $oDbResult = $this->_getModel()->getByWhere('sl_contact', $sWhere, 'count(*) as nb_contact');
      $oDbResult->readFirst();
      $nContact = (int)$oDbResult->getFieldValue('nb_contact');
      if($nContact > 3)
        $nContact = 3;
      elseif($nContact == 0)
        $nContact = -1;

      $nScore+= ($nContact * 10);

      if($nScore > 116)
        $nRating = 100;
      else
      {
        $nRating = round(($nScore / 116)*100, 2);

        if($nRating < 0)
          $nRating = 1;
        else if($nRating > 100)
          $nRating = 100;
      }

      return $nRating;
    }

    public function updateCandidateProfile($pnCandidatePk)
    {

      if(!assert('is_key($pnCandidatePk)'))
        return array();

      $asData = $this->_getModel()->getCandidateData($pnCandidatePk, true);
      if(empty($asData))
        return array();

      $nScore = 0;
      if(!empty($asData['languagefk']))
        $nScore+= 3;

      if(!empty($asData['nationalityfk']))
        $nScore+= 3;

      if(!empty($asData['locationfk']))
        $nScore+= 3;

      if(!empty($asData['date_birth']))
      {
        if($asData['is_birth_estimation'])
          $nScore+= 3;
        else
          $nScore+= 5;
      }


      if($asData['cpa'] != null || $asData['mba'] != null)
        $nScore+= 3;

      if(!empty($asData['skill_ag']))
        $nScore+= 3;
      if(!empty($asData['skill_ap']))
        $nScore+= 3;
      if(!empty($asData['skill_am']))
        $nScore+= 3;
      if(!empty($asData['skill_mp']))
        $nScore+= 3;
      if(!empty($asData['skill_in']))
        $nScore+= 3;
      if(!empty($asData['skill_ex']))
        $nScore+= 3;
      if(!empty($asData['skill_fx']))
        $nScore+= 3;
      if(!empty($asData['skill_ch']))
        $nScore+= 3;
      if(!empty($asData['skill_ed']))
        $nScore+= 3;
      if(!empty($asData['skill_pl']))
        $nScore+= 3;
      if(!empty($asData['skill_e']))
        $nScore+= 3;

      if(!empty($asData['title']))
        $nScore+= 5;

      if(!empty($asData['department']))
        $nScore+= 5;

      if(!empty($asData['keyword']))
        $nScore+= 3;
      if(!empty($asData['salary']))
        $nScore+= 5;
      if(!empty($asData['bonus']))
        $nScore+= 5;
      if(!empty($asData['target_low']))
        $nScore+= 3;
      if(!empty($asData['target_high']))
        $nScore+= 3;


      //Update _has_doc and used for quality ratio
      $asItem = array(CONST_CP_UID => $this->csUid, CONST_CP_ACTION => CONST_ACTION_VIEW, CONST_CP_TYPE => CONST_CANDIDATE_TYPE_CANDI, CONST_CP_PK => $pnCandidatePk);
      $oShareSpace = CDependency::getComponentByName('sharedspace');
      $asDocument = $oShareSpace->getDocuments(-1, $asItem);
      //dump($asDocument);
      $nDocument = (int)!empty($asDocument);
      if($nDocument == 0)
        $nScore+= -10;
      else
        $nScore+= 10;

      //calculating ratio
      $oNote = CDependency::getComponentByName('sl_event');
      $asCharNote = $oNote->getNotes($pnCandidatePk, CONST_CANDIDATE_TYPE_CANDI, 'character');
      //dump($asCharNote);
      $asNote = $oNote->getNotes($pnCandidatePk, CONST_CANDIDATE_TYPE_CANDI, '', array('character'));
      //dump($asNote);

      $nCharNote = count($asCharNote);
      if($nCharNote > 3)
        $nCharNote = 3;

      $nNote = count($asNote);
      if($nNote > 3)
        $nNote = 3;

      if(empty($nCharNote) && empty($nNote))
        $nScore-= 15;
      else
        $nScore+= ($nCharNote * 7) + ($nNote * 7);


      $sWhere = ' item_type = "'.CONST_CANDIDATE_TYPE_CANDI.'" AND itemfk = "'.$pnCandidatePk.'" ';
      $oDbResult = $this->_getModel()->getByWhere('sl_contact', $sWhere, 'count(*) as nb_contact');
      $oDbResult->readFirst();
      $nContact = (int)$oDbResult->getFieldValue('nb_contact');
      if($nContact > 3)
        $nContact = 3;
      elseif($nContact == 0)
        $nContact = -1;

      $nScore+= ($nContact * 10);

      //Update _in_play (nb of active positions)
      $oPosition = CDependency::getComponentByName('sl_position');
      $nPlay = $oPosition->isCandidateInPlay($pnCandidatePk);
      $sLimit = date('Y-m-d', strtotime('-1 year'));
      $sLimit2 = date('Y-m-d', strtotime('-2 years'));
      $sLimit2 = date('Y-m-d', strtotime('-2 years'));
      $sLimit3 = date('Y-m-d', strtotime('6 months'));

      // store the most relevant position activity
      //look for active status first... More priority than any other
      $nMaxActiveStatus = $oPosition->getMaxActiveStatus($pnCandidatePk, 100);

      /*
      * Getting to complicated, let's make a dedicated query. See below

      //if nothiing active, lets extend to "pitched but inactive" status
      if(empty($nMaxActiveStatus))
        $nMaxActiveStatus = $oPosition->getLastInactiveStatus($pnCandidatePk, 101, $sLimit);

      $sLimit = date('Y-m-d', strtotime('-6 months'));

      //if nothiing active, lets extend to "pitched but inactive" status
      if(empty($nMaxActiveStatus))
        $nMaxActiveStatus = $oPosition->getMaxActiveStatus($pnCandidatePk, 201, $sLimit);

      //then if there's nothing... let's use the last status of any kind
      if(empty($nMaxActiveStatus))
        $nMaxActiveStatus = $oPosition->getLastInactiveStatus($pnCandidatePk, 250, $sLimit);*/

      if(empty($nMaxActiveStatus))
      {
        /*
         * ,
          IF(`status` = 101 AND `date_created` >= \''.$sLimit.'\', 1, 0) as placed,
          IF(`status` = 101 AND `date_created` >= \''.$sLimit2.'\', 1, 0) as placed2,
          IF(`active` = 0 AND `date_created` >= \''.$sLimit3.'\', 1, 0) as considered
         */
        $sQuery = 'SELECT *
          FROM `sl_position_link`
          WHERE `candidatefk` = '.$pnCandidatePk.'
          ORDER BY
          IF(`status` = 101 AND `date_created` >= \''.$sLimit.'\', 1, 0) DESC,
          active DESC,
          IF(`status` = 101 AND `date_created` >= \''.$sLimit2.'\', 1, 0) DESC,
          IF(`active` = 0 AND `date_created` >= \''.$sLimit3.'\', 1, 0) DESC,
          `date_created` DESC

          LIMIT 1 ';
        $oDbResult = $this->_getModel()->executeQuery($sQuery);
        $oDbResult->readFirst();
        $nMaxActiveStatus = (int)$oDbResult->getFieldValue('status');
      }

      //dump($nPlay);
      //dump($nMaxActiveStatus);

      if($nScore > 116)
        $nRating = 100;
      else
      {
        $nRating = round(($nScore / 116)*100, 2);

        if($nRating < 0)
          $nRating = 1;
        else if($nRating > 100)
          $nRating = 100;
      }

      $asUpdate = array('_has_doc' => $nDocument, '_in_play' => $nPlay, '_pos_status' => $nMaxActiveStatus,
          'profile_rating' => $nRating, '_date_updated' => date('Y-m-d H:i:s'));
      $bUpdated = $this->_getModel()->update($asUpdate, 'sl_candidate_profile', 'candidatefk = '.$pnCandidatePk);

      if(!$bUpdated)
      {
        assert('false; /* could not update candidate profile - cron updateProfile */');
      }

      return $asUpdate;
    }

    //END save candidate form
    /* *********************************************************** */



    public function _getTreeData($psType)
    {
      if(!assert('$psType == \'occupation\' || $psType == \'industry\' '))
        return array();

      $sTable = 'sl_'.$psType;
      $sKey = 'sl_'.$psType.'pk';

      if($psType == 'occupation')
        $asItemList = $this->getVars()->getOccupationList(true, true);
      else
        $asItemList = $this->getVars()->getIndustryList(true, true);

      //$oDbResult = $this->_getModel()->getByWhere($sTable);

      $sQuery = 'SELECT main.* FROM '.$sTable.' as main
        LEFT JOIN '.$sTable.' as parent ON (parent.'.$sKey.' = main.parentfk)
        ORDER BY parent.label, main.label ';
      $oDbResult = $this->_getModel()->executeQuery($sQuery);

      $bRead = $oDbResult->readFirst();
      if(!$bRead)
        return array();

      $asTree = array();
      while($bRead)
      {
        $asData = $oDbResult->getData();
        //make the field generic usic parent/value attributes
        $asData['parent'] = $asData['parentfk'];
        $asData['value'] = $asData[$sKey];

        if($asData['parentfk'] == 0 || !isset($asItemList[$asData['parentfk']]))
          $asData['level'] = 0;
        else
        {
          if($asItemList[$asData['parentfk']]['parentfk'] == 0)
            $asData['level'] = 1;
          else
            $asData['level'] = 2;
        }

        $asTree[$asData[$sKey]] = $asData;
        $bRead = $oDbResult->readNext();
      }

      //dump($asTree);

      return $asTree;
    }


    public function getCandidateRm($pnCandidatePk, $pbActiveOnly = true, $pbFriendly = false)
    {
      if(!assert('is_key($pnCandidatePk)'))
        return array();

      return $this->_getModel()->getCandidateRm($pnCandidatePk, $pbActiveOnly, $pbFriendly);
    }

    private function _accessRmContactDetails($pnCandidatePk)
    {
      if(!assert('is_key($pnCandidatePk)'))
        return false;


      $asCandidate = $this->_getModel()->getCandidateData($pnCandidatePk, true, true);
      if(!assert('!empty($asCandidate)'))
        return false;

      $asRm = $this->_getModel()->getCandidateRm($pnCandidatePk);
      if(!assert('!empty($asRm)'))
        return false;


      $asCandidate = $asCandidate[$pnCandidatePk];
      $oLogin = CDependency::getCpLogin();


      $sUrl = $this->_oPage->getUrl('555-001', CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, $pnCandidatePk);

      $sSubject = 'Slistem RM - Access to '.$asCandidate['firstname'].' '.$asCandidate['lastname'].' (#'.$pnCandidatePk.') contact details';

      $sContent = 'Hello,<br /><br />
        '.$oLogin->getCurrentUserName().' has accessed <strong style="color: #555555;">'.$asCandidate['firstname'].' '.$asCandidate['lastname'].'</strong> (#'.$pnCandidatePk.') contact details.<br />
        <br />
        Candidate:<br />
        refId = '.$this->_oDisplay->getLink('#'.$pnCandidatePk, $sUrl).'<br />
        Company: '.$asCandidate['company_name'].'<br />
        Title: '.$asCandidate['title'].'<br />
        Department: '.$asCandidate['department'].'<br />
        Created: '.$asCandidate['date_created'].'<br />';

      //$sContent.= '<hr /> '.var_export($asRm, true);

      $oMail = CDependency::getComponentByName('mail');
      $oMail->createNewEmail();
      $oMail->setFrom(CONST_PHPMAILER_EMAIL, CONST_PHPMAILER_DEFAULT_FROM);

      $nCount = 0;
      foreach($asRm as $asUser)
      {
        if($nCount == 0)
          $oMail->addRecipient($asUser['email'], $asUser['name']);
        else
          $oMail->addCCRecipient($asUser['email'], $asUser['name']);

        $nCount++;
      }

      $bNotified = $oMail->send($sSubject, $sContent);
      if($bNotified)
        $_SESSION['sl_candidate']['contact_acccess'][$pnCandidatePk] = time();

      return true;
    }



    private function _getResumeAddForm()
    {
      $nCandidatePk = (int)getValue('cp_pk');
      if(!assert('is_key($nCandidatePk)'))
        return '';

      $oPage = CDependency::getCpPage();
      $oPage->addCustomJs('
        function loadTinyMce(psUrl, psFieldId, pbIsHtml)
        {
          $.ajax(
          {
            url: psUrl,
            dataType: "html",
            success: function(sData)
            {
              tinymce.get(psFieldId).setProgressState(1);

              if(pbIsHtml)
              {
                sData = sData+"<p />" + tinymce.get(psFieldId).getContent();
                tinymce.get(psFieldId).setContent(sData, {format : "raw"});
              }
              else
              {
                sData = sData+"\n" + tinymce.get(psFieldId).getContent();
                tinymce.get(psFieldId).setContent(sData);
              }
              tinymce.get(psFieldId).save();
              tinymce.get(psFieldId).setProgressState(0);
            }
          });
        }
      ');


      $sTitle = getValue('document_title');

      $oForm = $this->_oDisplay->initForm('resumeAddForm');
      $sURL = $oPage->getAjaxUrl($this->csUid, CONST_ACTION_SAVEADD, CONST_CANDIDATE_TYPE_DOC, $nCandidatePk);

      $oForm->setFormParams('addresume', true, array('action' => $sURL, 'class' => 'resumeCreateForm', 'submitLabel'=>'Create a resume'));
      $oForm->setFormDisplayParams(array('noCancelButton' => true, 'columns' => 1));

      $oForm->addField('input', 'candidatepk', array('type' => 'hidden','value'=> $nCandidatePk));
      $oForm->addField('input', 'userfk', array('type' => 'hidden', 'value' => $this->casUserData['pk']));
      $oForm->addField('input', 'pclose', array('type' => 'hidden', 'value' => getValue('pclose')));

      $oForm->addField('input', 'cp_uid', array('type' => 'hidden', 'value' => getValue('cp_uid')));
      $oForm->addField('input', 'cp_action', array('type' => 'hidden', 'value' => getValue('cp_action')));
      $oForm->addField('input', 'cp_type', array('type' => 'hidden', 'value' => getValue('cp_type')));
      $oForm->addField('input', 'cp_pk', array('type' => 'hidden', 'value' => getValue('cp_pk')));

      $oForm->addField('misc', '', array('type' => 'title', 'title'=> 'Create a resume'));
      $oForm->addField('misc', '', array('type' => 'br'));


      $oForm->addField('input', 'title', array('type' => 'text', 'label' => 'Title', 'value' => $sTitle));

      $oForm->addField('textarea', 'content', array('type' => 'br', 'label' => 'Content', 'isTinymce' => 1, 'class' => 'resume_mce', 'style' => 'height: 410px;'));
      $oForm->setFieldControl('content', array('jsFieldNotEmpty' => '', 'jsFieldMinSize' => '50'));
      $oForm->setFieldDisplayParams('content', array('class' => 'fullWidthMce'));

      $sURL = $this->getResourcePath().'/resume/resume_template.html';
      $sJavascript = 'var sMceId = $(this).closest(\'form\').find(\'.resume_mce\').attr(\'id\'); loadTinyMce(\''.$sURL.'\', sMceId, true); ';
      $oForm->addField('misc', '', array('type' => 'text', 'text' => '<a href="javascript:;" onclick="'.$sJavascript.'">Load template 1</a>'));

      return $oForm->getDisplay();
    }


    private function _getResumeSaveAdd($array = '' , $passTitle ='Resume')
    {
;
      // check form, create a html file from it

      if(isset($array[0]))
      {
        $pasCandidate = $array[0];
        $sContent = $array[1];
        $sTitle = $passTitle;
      }
      else
      {
        $sTitle = trim(getValue('title'));
        $sContent = purify_html(getValue('content'));
      }


      if(empty($sTitle) || empty($sContent))
        return array('error' => 'Title and resume content are required.');

      $head = '
        <html>
          <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
          </head>
          <body>';

      $footer = '
          </body>
        </html>';

      $sContent = $head.$sContent.$footer;

      $asCpLink = array();

      if(isset($array[0]))
      {
        $asCpLink['cp_uid'] = '555-001';
        $asCpLink['cp_action'] = 'ppav';
        $asCpLink['cp_type'] = 'candi';
        $asCpLink['cp_pk'] = $pasCandidate;
      }
      else
      {
        $asCpLink['cp_uid'] = getValue('cp_uid');
        $asCpLink['cp_action'] = getValue('cp_action');
        $asCpLink['cp_type'] = getValue('cp_type');
        $asCpLink['cp_pk'] = (int)getValue('cp_pk');
      }


      if(!assert('is_cpValues($asCpLink)'))
        return array('error' => 'Missing parameters.');

      //save the file in the temp folder
      $sFileName = uniqid('resume_html_').'.html';
      $sFilePath = $_SERVER['DOCUMENT_ROOT'].'/tmp/'.$sFileName;
      try
      {
        $oFs = fopen($sFilePath, 'a+');
        fputs($oFs, $sContent);
      }
      catch(Exception $oExcept)
      {
        return array('error' => __LINE__.' - Error saving the resume. '.$oExcept->getMessage());
      }

      if($oFs)
        fclose($oFs);

      $asToRemove = array('?', '[', ']', '/', '\\', '=', '<', '>', ':', ';', ',', "'", '"', '&', '$', '#', '*', '(', ')', '|', '~', '`', '!', '{', '}', '¥', ' ');
      $sDisplayFileName = str_replace($asToRemove, '_', $sTitle).'.html';


      //can't use curl here (session issue ... need to re identify myself :/
      //so call straight shared space
      $oSharedspace = CDependency::getComponentByName('sharedspace');
      $sError = $oSharedspace->saveLocalDocument($sDisplayFileName, $sFilePath, $sTitle, 'resume', $asCpLink);

      if(isset($array[0]))
      {
        return true;
      }
      else
      {
        if(!empty($sError))
          return array('error' => $sError);


        $this->_getModel()->_logChanges(array('sl_document' => 'new'), 'document', 'new document', '', $asCpLink);

        $sURL = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, $asCpLink['cp_pk'], array('check_profile' => 1));


        if(getValue('pclose'))
          return array('notice' => 'Resume saved.', 'action' => 'view_candi("'.$sURL.'", "#tabLink3");');

        return array('notice' => 'Resume saved.', 'action' => 'view_candi("'.$sURL.'", "#tabLink3"); goPopup.removeActive(\'layer\');  ');
        }
    }


    private function _getViewLastDocument($pnCandidatePk)
    {
      if(!assert('is_key($pnCandidatePk)'))
        return array('error' => __LINE__.' - Can not find the candidate profile.');


      $oShareSpace = CDependency::getComponentByName('sharedspace');


      $asItem = array(CONST_CP_UID => $this->csUid, CONST_CP_ACTION => CONST_ACTION_VIEW, CONST_CP_TYPE => CONST_CANDIDATE_TYPE_CANDI, CONST_CP_PK => $pnCandidatePk);
      $asDocument = $oShareSpace->getDocuments(0, $asItem);
      if(empty($asDocument))
      {
        $sMessage = '<div style="padding: 10px; margin: 10px; border: 1px solid red; background-color: #FFC6BC;">An error occured, no document found for this candidate.</div>';
        exit($sMessage);
        //return array('error' => 'Not document found for this candidate.');
      }

      $asFirst = array_first($asDocument);
      return $oShareSpace->viewDocument((int)$asFirst['documentpk']);
    }


    /**
     * Check if a specific sql array is empty
     * @param type $pasArray
     * @return boolean
     */
    private function _sqlArrayEmpty($pasArray)
    {
      if(empty($pasArray['required']) && empty($pasArray['optional']))
        return true;

      return false;
    }

    /**
     * Implode a specific sql array
     * @param type $pasArray
     * @return string
     */
    private function _sqlImplode($psGlue, $pasArray)
    {
      if(empty($pasArray['optional']))
        return implode($psGlue, $pasArray['required']);

      if(empty($pasArray['required']))
        return implode($psGlue, $pasArray['optional']);

      return implode($psGlue, $pasArray['required']).$psGlue.implode($psGlue, $pasArray['optional']);
    }



    private function _autocompleteSearch($psType)
    {
      $sSearchString = getValue('q');
      if(empty($sSearchString))
        return array();

      if($psType == CONST_CANDIDATE_TYPE_INDUSTRY)
        $sTable = 'sl_industry';
      else
        $sTable = 'sl_occupation';


      $oDb = CDependency::getComponentByName('database');

      if($sSearchString == 'all' || $sSearchString == 'more')
      {
        $sQuery = 'SELECT item.*,
          parent.'.$sTable.'pk as parentId, parent.label as parentLabel, parent.parentfk as parentParent,
          child.'.$sTable.'pk as childId, child.label as childLabel ';

        $sQuery.= ' FROM '.$sTable.' as item';
        $sQuery.= ' LEFT JOIN '.$sTable.' as parent ON (parent.'.$sTable.'pk = item.parentfk) ';
        $sQuery.= ' LEFT JOIN '.$sTable.' as child ON (child.parentfk = item.'.$sTable.'pk) ';
        $sQuery.= ' ORDER BY item.parentfk ASC, item.label ASC ';

      }
      else
      {
        $sQuery = 'SELECT item.*,
          parent.'.$sTable.'pk as parentId, parent.label as parentLabel, parent.parentfk as parentParent,
          child.'.$sTable.'pk as childId, child.label as childLabel,

          IF(item.label LIKE '.$oDb->dbEscapeString($sSearchString).', 1, 0) as nEqual,
          IF(item.label LIKE '.$oDb->dbEscapeString('%'.$sSearchString).', 1, 0) as nStart ';

        $sQuery.= ' FROM '.$sTable.' as item
          LEFT JOIN '.$sTable.' as parent ON (parent.'.$sTable.'pk = item.parentfk)
          LEFT JOIN '.$sTable.' as child ON (child.parentfk = item.'.$sTable.'pk)
          WHERE ( item.label LIKE '.$oDb->dbEscapeString('%'.$sSearchString.'%').'
          OR item.label LIKE '.$oDb->dbEscapeString(trim(substr($sSearchString, 0, 3)).'%').' )

          ORDER BY item.parentfk ASC, nEqual DESC, nStart DESC, item.label ASC ';
      }


      //$oDbResult = $this->_getModel()->getByWhere($sTable, $sWhere, $sSelect, ', '50');
      $oDbResult = $oDb->ExecuteQuery($sQuery);
      $bRead = $oDbResult->readFirst();

      $asJsonData = array();
      while($bRead)
      {
        $asData = $oDbResult->getData();
        $asEntry = array();
        $asEntry['type'] = $sTable;

        if(!empty($asData['parentId']))
        {
          $asEntry['id'] = $asData['parentId'];
          $asEntry['name'] = $asData['parentLabel'];
          $asEntry['parent'] = 1;

          if(empty($asData['parentParent']))
            $asEntry['level'] = 0;
          else
            $asEntry['level'] = 1;

          $asJsonData[$asEntry['id']] = json_encode($asEntry);
        }

        $asEntry['id'] = $asData[$sTable.'pk'];
        $asEntry['name'] = $asData['label'];
        $asEntry['parent'] = (int)empty($asData['childId']);
        $asEntry['level'] = 0;

        if(!empty($asData['parentfk']))
          $asEntry['level']++;

        if(!empty($asData['parentParent']))
            $asEntry['level']++;

        $asJsonData[$asEntry['id']] = json_encode($asEntry);

        if(!empty($asData['childId']))
        {
          $asEntry['id'] = $asData['childId'];
          $asEntry['name'] = $asData['childLabel'];
          $asEntry['parent'] = 0;

          $asEntry['level'] = 0;
          if(!empty($asData['parentId']))
            $asEntry['level']++;

          if(empty($asData['parentParent']))
            $asEntry['level']++;

          $asJsonData[$asEntry['id']] = json_encode($asEntry);
        }

        $bRead = $oDbResult->readNext();
      }

      exit('['.implode(',', $asJsonData).']');

    }


    private function _isActiveConsultant($pnConsultant)
    {
      if(!assert('is_key($pnConsultant)'))
        return false;

      if(empty($_SESSION['sl_candidate_active_user']))
      {
        $oLogin = CDependency::getCpLogin();
        $_SESSION['sl_candidate_active_user'] = $oLogin->getUserList(0, true);
      }

      if(isset($_SESSION['sl_candidate_active_user'][$pnConsultant]))
        return true;

      return false;
    }


    /**
     * Candidate selector; searching by name or refID
     */
    function _autocompleteCandidate()
    {
      $sSearchString = trim(getValue('q'));
      if(empty($sSearchString))
      {
        $asEntry = array();
        $asEntry['id'] = 'token_clear';
        $asEntry['name'] = 'Nothing to search for';
        $asJson[$asEntry['id']] = json_encode($asEntry);
        exit('['.implode(',', $asJson).']');
      }

      $asWords = explode(' ', trim($sSearchString));

      foreach($asWords as $nKey => $sWord)
      {
        if(empty($sWord) || strlen($sWord) < 2)
          unset($asWords[$nKey]);
      }


      $nWord = count($asWords);
      if($nWord < 1)
      {
        $asEntry = array();
        $asEntry['id'] = 'token_clear';
        $asEntry['name'] = 'A refId, firstname and/or lastname are required. (2 character min each)';
        $asJson[$asEntry['id']] = json_encode($asEntry);
        exit('['.implode(',', $asJson).']');
      }

      $poQB = $this->_getModel()->getQueryBuilder();
      $poQB->setTable('sl_candidate', 'scan');


      $sSearchString = str_replace(array('#', '"', ',', '.'), '', $sSearchString);
      $sRefId = preg_replace('/[^0-9\#]/i', '', $sSearchString);
      if((int)$sRefId == (int)$sSearchString && (int)$sRefId > 0)
      {
        $poQB->addSelect('scan.*');
        $poQB->addWhere('scan.sl_candidatepk = '.(int)$sRefId);
      }
      else
      {
        if($nWord == 1)
        {
          //must be the lastname
          $poQB->addSelect('scan.*, levenshtein("'.$this->_getModel()->dbEscapeString($asWords[0]).'", LOWER(scan.lastname)) AS lastname_lev ');
          $poQB->addWhere('scan.lastname LIKE '.$this->_getModel()->dbEscapeString($asWords[0].'%'));
          $poQB->addOrder('lastname_lev ASC, scan.firstname ASC');
        }
        else
        {
          //We don't know soooo... we try different combinaisons with the words we have
          $poQB->addSelect('scan.*');
          $_POST['qs_super_wide'] = 0;
          $_POST['qs_wide'] = 0;
          $_POST['qs_name_format'] = 'none';
          $_POST['candidate'] = $sSearchString;
          $_POST['data_type'] = 'candi';

          require_once('component/sl_candidate/resources/search/quick_search.class.php5');
          $oQS = new CQuickSearch($poQB);
          $oQS->buildQuickSearch();
        }
      }

      $poQB->addLIMIT('0,100');

      $asJsonData = array();
      $oDbResult = $this->_getModel()->executeQuery($poQB->getSql());
      $bRead = $oDbResult->readFirst();
      if(!$bRead)
      {
        $asEntry = array();
        $asEntry['id'] = 'token_clear';
        $asEntry['name'] = 'No candidate matching.';
        $asJsonData[$asEntry['id']] = json_encode($asEntry);
        exit('['.implode(',', $asJsonData).']');
      }

      if($oDbResult->numRows() >= 100)
      {
        $asEntry['id'] = 'token_clear';
        $asEntry['name'] = 'Only 100 results displayed. Please refine your search...';
        $asJsonData[$asEntry['id']] = json_encode($asEntry);
      }

      while($bRead)
      {
        $asCandidate = $oDbResult->getData();
        $asEntry = array();

        $asEntry['id'] = $asCandidate['sl_candidatepk'];
        $asEntry['name'] = '  #'.$asCandidate['sl_candidatepk'].'  - '.$asCandidate['lastname'].' '.$asCandidate['firstname'];
        $asJsonData[$asEntry['id']] = json_encode($asEntry);

        $bRead = $oDbResult->readNext();
      }



      exit('['.implode(',', $asJsonData).']');
    }

    function _autocompleteCompany()
    {

      $sSearchString = getValue('q');
      if(empty($sSearchString))
      {
        $asEntry = array();
        $asEntry['id'] = 'token_clear';
        $asEntry['name'] = 'Nothing to search for';
        $asJson[$asEntry['id']] = json_encode($asEntry);
        exit('['.implode(',', $asJson).']');
      }

      require_once('component/sl_candidate/resources/search/quick_search.class.php5');

      $poQB = $this->_getModel()->getQueryBuilder();
      $poQB->setTable('sl_company', 'scom');



      $sRefId = CQuickSearch::_fetchRefIdFromString($sSearchString);
      if((string)$sRefId == $sSearchString || ('#' . $sRefId) == $sSearchString)
      {
        $poQB->addSelect('scom.*');
        $poQB->addWhere('scom.sl_companypk = '.(int)$sRefId);
        $poQB->addWhere(" scom.merged_company_id = '0' ");
      }
      else
      {

        $asWords = explode(' ', trim($sSearchString));

        foreach($asWords as $nKey => $sWord)
        {
          if(empty($sWord))
            unset($asWords[$nKey]);
        }

        if(empty($asWords))
        {
          $asEntry = array();
          $asEntry['id'] = 'token_clear';
          $asEntry['name'] = 'Company name should be at least 2 character long.';
          $asJson[$asEntry['id']] = json_encode($asEntry);
          exit('['.implode(',', $asJson).']');
        }

        $escapedString = $this->_getModel()->dbEscapeString($sSearchString);
        $stringCount = strlen($escapedString);
        $stringCount = $stringCount-2; // iki adet " geliyor o nedenle -2
        //ChromePhp::log($escapedString);
        //ChromePhp::log($stringCount);

        $poQB->addSelect('scom.*, IF(scom.name LIKE '.$this->_getModel()->dbEscapeString($sSearchString).', 1, 0) as exact_name ');

        $poQB->addSelect('scom.*, IF(LEFT(scom.name , '.$stringCount.') LIKE '.$this->_getModel()->dbEscapeString($sSearchString).', 1, 0) as exact_name2 ');

        foreach($asWords as $nKey => $sWord)
          $asWords[$nKey] = '(scom.name LIKE '.$this->_getModel()->dbEscapeString($sWord.'%').' )';

        $implode =implode(' OR ',$asWords);
        $implode = " ( ".$implode." ) ";
        $poQB->addWhere($implode);
        $poQB->addWhere(" scom.merged_company_id = '0' ");

        $poQB->addOrder('exact_name DESC, exact_name2 DESC, scom.name ASC');
      }

      $createdSql = $poQB->getSql();
      //ChromePhp::log($createdSql);

      $oDbResult = $this->_getModel()->executeQuery($poQB->getSql());
      $bRead = $oDbResult->readFirst();
      if(!$bRead)
      {
        $asEntry = array();
        $asEntry['id'] = 'token_clear';
        $asEntry['name'] = 'No company matching.';
        $asJson[$asEntry['id']] = json_encode($asEntry);
        exit('['.implode(',', $asJson).']');
      }

      $asJsonData = array();
      while($bRead)
      {
        $asCandidate = $oDbResult->getData();
        $asEntry = array();

        $asCandidate['name'] = preg_replace('/[^a-z0-9\.,#\'" &]/i', '', $asCandidate['name']);

        $asEntry['id'] = $asCandidate['sl_companypk'];
        $asEntry['name'] = '  #'.$asCandidate['sl_companypk'].' - '.mb_strimwidth($asCandidate['name'], 0, 38, '...');
        $asEntry['title'] = $asCandidate['name'];
        $asJsonData[$asEntry['id']] = json_encode($asEntry);

        $bRead = $oDbResult->readNext();
      }

      exit('['.implode(',', $asJsonData).']');
    }


    private function _getNoScoutList()
    {
      $oPage = CDependency::getCpPage();
      $oLogin = CDependency::getCpLogin();
      $oHTML = CDependency::getCpHtml();

      $oPage->addCssFile(self::getResourcePath().'css/no_scout_list.css');
      $nLevel = (int)getValue('filter_level', 0);

      if(!empty($nLevel))
      {// parantez icinde OR is_nc_ok = 0 vardi kaldirdik
        $sQuery = 'SELECT * FROM sl_company WHERE level = '.$nLevel.' AND (is_client = 1) ORDER BY name ASC';
      }
      else // OR is_nc_ok = 0 vardi kaldirdik
        $sQuery = 'SELECT * FROM sl_company WHERE level in(1,2,3) AND is_client = 1  ORDER BY name ASC';

      //ChromePhp::log($sQuery);

      $oDbResult = $this->_getModel()->executeQuery($sQuery);
      $bRead = $oDbResult->readFirst();

      $asLetter = array(1=>'A', 2=>'B', 3=>'C');
      $asCompany = array();
      $asLetters = array();
      $nCount = 0;
      while($bRead)
      {
        $asCpData = $oDbResult->getData();

        if (empty($asLetter[$asCpData['level']]))
        {
          $bRead = $oDbResult->readNext();
          continue;
        }

        $company_id = $asCpData['sl_companypk'];
        $employeeCount = getCompanyEmployeeCount($company_id);

        $companyOwners = getCompanyOwner($company_id);
        $owner_names = '';
        foreach ($companyOwners as $key => $companyOwner)
        {
          $owner_id = $companyOwner['owner'];
          $owner_names.= $oLogin->getUserLink((int)$companyOwner['owner'],false,false,true).', ';
          //$user_information = getUserInformaiton($owner_id);
          //$owner_names.= $user_information['firstname'].',';
        }
        $owner_names = trim($owner_names, ", ");

        if(empty($owner_names))
        {
          $owner_names = $oLogin->getUserLink(101,false,false,true);
        }
        //ChromePhp::log($owner_names);

        $asCpData['level_letter'] = $asLetter[$asCpData['level']];
        $sFirstLetter = strtoupper(substr($asCpData['name'], 0, 1));
        if(is_numeric($sFirstLetter))
          $sFirstLetter = '#';

        $sURL = $oPage->getAjaxUrl($this->csUid, CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_COMP, (int)$asCpData['sl_companypk']);

        $sCompany = '<div class="cp_ns_row">
            <div class="cp_quality qlt_'.$asCpData['level_letter'].'">'.$asCpData['level_letter'].'</div>
            <div class="cp_id">#'.$asCpData['sl_companypk'].'</div>
            <div class="cp_name"><a href="javascript:;" onclick="popup_candi(this, \''.$sURL.'\');">'.$asCpData['name'].'</div>
            <div class="cp_consultant">'.$owner_names.'</div>
            <div class="cp_update">'.substr($asCpData['date_updated'], 0, 10).'&nbsp;</div>
            <div class="cp_employee">'.$employeeCount.'&nbsp;</div>
          </div>';
// employeeCount yerine $asCpData['num_employee'] vardi

        $asCompany[$sFirstLetter][] = $sCompany;
        $asLetters[$sFirstLetter] = $oHTML->getLink($sFirstLetter, '#'.$sFirstLetter);

        $nCount++;
        $bRead = $oDbResult->readNext();
      }

      $sHTML = $oHTML->getTitle($nCount.' Companies in the list', 'h3', true);

        $sURL = $oPage->getUrl($this->csUid, CONST_ACTION_LIST, CONST_CANDIDATE_TYPE_COMP);
        $sHTML.= $oHTML->getBlocStart('',  array('style' => 'line-height: 20px; font-style: italic; color: #777;'));
        $sHTML.= 'Filter by company level&nbsp;&nbsp;<select onchange="document.location.href = $(this).val();" >';
        $sHTML.= '<option value="'.$sURL.'" > All </option>';
        $sHTML.= '<option value="'.$sURL.'&filter_level=1" '.(($nLevel == 1)? 'selected="selected"' : '').'> A </option>';
        $sHTML.= '<option value="'.$sURL.'&filter_level=2" '.(($nLevel == 2)? 'selected="selected"' : '').'> B </option>';
        $sHTML.= '<option value="'.$sURL.'&filter_level=3" '.(($nLevel == 3)? 'selected="selected"' : '').'> C </option>';
        $sHTML.= '</select>';
        $sHTML.= $oHTML->getBlocEnd();
      $sHTML.= $oHTML->getCR();

      $sHTML.= $oHTML->getBlocStart('', array('class' => 'ns_list_container'));

        $asTabs = array();
        foreach($asCompany as $sLetter => $asCompany)
        {
          $sBlock = $oHTML->getBlocStart('', array('class' => 'ns_list_block'));
          $sBlock.= $oHTML->getTitle($sLetter.' ('.count($asCompany).')', 'h3', true);

          $sBlock.= '<div class="cp_ns_row header">
            <div class="cp_quality">Level</div>
            <div class="cp_id">refId</div>
            <div class="cp_name">Company name</div>
            <div class="cp_consultant">Owner(s)</div>
            <div class="cp_update">Last update</div>
            <div class="cp_employee">Nb employee</div>
          </div>';
          $sBlock.= implode('', $asCompany);
          $sBlock.= $oHTML->getFloatHack();
          $sBlock.= $oHTML->getLink('back to top &uarr;', 'javascript:;', array('onclick' => '$(this).closest(\'.scrollingContainer\').animate({scrollTop: 0}, 450);'));
          $sBlock.= $oHTML->getBlocEnd();

          $asTabs[] = array('label' => 'tab_'.$sLetter, 'title' => $sLetter, 'content' => $sBlock);
        }

      $sHTML.= $oHTML->getTabs('ns_list', $asTabs, '', 'inline', true);
      $sHTML.= $oHTML->getBlocEnd();

      return $sHTML;
    }

    //------------------------------------------------------
    //  Public methods
    //------------------------------------------------------

    /**
     * return an array with all the candidate profile data
     * @param integer $pnPk
     * @param boolean $pbFullProfile
     * @return array()
     */
    public function getCandidateData($pnPk, $pbFullProfile = false)
    {
      if(!assert('is_key($pnPk) && is_bool($pbFullProfile)'))
        return array();

      return $this->_getModel()->getCandidateData($pnPk, $pbFullProfile);
    }


    /**
     * Update candidate data and update profile status if requested
     *
     * @param array $asUpdateData
     * @param integer $pnCandidatePk
     * @param boolean $pbUpdateStatus
     * @return boolean
     */
    public function quickUpdateProfile($asUpdateData, $pnCandidatePk, $pbUpdateStatus = false)
    {
      if(!assert('is_key($pnCandidatePk) && is_array($asUpdateData) && !empty($asUpdateData)'))
        return false;


      $vResult = $this->_getModel()->update($asUpdateData, 'sl_candidate_profile', 'candidatefk = '.$pnCandidatePk);

      // if company changed, or active positions... need to refresh all statuses
      if($pbUpdateStatus)
        $this->updateCandidateProfile($pnCandidatePk);

      return $vResult;
    }













    private function _getRmList($pnCandidatePk)
    {
      if(!assert('is_key($pnCandidatePk)'))
        return array('error' => 'Sorry, an error occured.');

      $sHTML = $this->_oDisplay->getBlocStart('', array('style' => 'float: right; padding: 3px 5px; background-color: #f0f0f0; border-color: #4C7696; color: #4C7696;'));
        $sURL = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_ADD, CONST_CANDIDATE_TYPE_RM, $pnCandidatePk);
        $sHTML.= '<a href="javascript:;" onclick="AjaxRequest(\''.$sURL.'\')">make me RM</a>';
        $sHTML.= $this->_oDisplay->getBlocEnd();
      $sHTML.= $this->_oDisplay->getFloathack();

      $sHTML.= $this->_oDisplay->getTitle('RM list', 'h3', true);


      $oDbResult = $this->_getModel()->getByWhere('sl_candidate_rm', 'candidatefk = '.$pnCandidatePk, '*', 'date_expired ASC,  date_started ASC');
      $bRead = $oDbResult->readFirst();
      if(!$bRead)
      {
        $sHTML.= 'No RM for this candidate';
        return array('data' => $sHTML);
      }



      $oLogin = CDependency::getCpLogin();
      $bIsAdmin = $oLogin->isAdmin();
      $nCurrentUserPk = $oLogin->getUserPk();
      $sPic = $this->_oDisplay->getPicture($this->getResourcePath().'/pictures/delete_16.png', 'cancel RM');
      $sPic2 = $this->_oDisplay->getPicture($this->getResourcePath().'/pictures/reload_16.png', 'extend RM period');
      $this->_oPage->addCssFile($this->getResourcePath().'css/rm.css');

      $sHTML.= $this->_oDisplay->getBlocStart('', array('class' => 'rm_container'));

      //List header
      $sRow = $this->_oDisplay->getBloc('', 'Status', array('class' => 'rm_status'));
      $sRow.= $this->_oDisplay->getBloc('', 'User', array('class' => 'rm_user'));
      $sRow.= $this->_oDisplay->getBloc('', 'Start', array('class' => 'rm_start'));
      $sRow.= $this->_oDisplay->getBloc('', 'End', array('class' => 'rm_end'));
      $sRow.= $this->_oDisplay->getBloc('', 'Actions', array('class' => 'rm_end'));
      $sHTML.= $this->_oDisplay->getBloc('', $sRow, array('class' => 'rm_row rm_header'));


      while($bRead)
      {
        $nLoginfk = (int)$oDbResult->getFieldValue('loginfk');
        $sStart = date('d M', strtotime($oDbResult->getFieldValue('date_started')));
        $sEnd = date('d M', strtotime($oDbResult->getFieldValue('date_ended')));
        $sAction = '&nbsp;';

        if($oDbResult->getFieldValue('date_expired'))
        {
          $sRow = $this->_oDisplay->getBloc('', 'expired', array('class' => 'rm_status'));
        }
        else
        {
          $sRow = $this->_oDisplay->getBloc('', 'active', array('class' => 'rm_status'));

          if($bIsAdmin || $nLoginfk == $nCurrentUserPk)
          {
            $sURL = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_EDIT, CONST_CANDIDATE_TYPE_RM, $pnCandidatePk, array('loginfk' => $nLoginfk));
            $sAction = '<a href="javascript:;" onclick="AjaxRequest(\''.$sURL.'\');">'.$sPic2.'</a>&nbsp;&nbsp; ';

            $sURL = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_DELETE, CONST_CANDIDATE_TYPE_RM, $pnCandidatePk, array('loginfk' => $nLoginfk));
            $sAction.= '<span class="cancel_rm"><a href="javascript:;" onclick="AjaxRequest(\''.$sURL.'\');">'.$sPic.'</a>';
          }
        }

        $sRow.= $this->_oDisplay->getBloc('', $oLogin->getuserLink($nLoginfk), array('class' => 'rm_user'));
        $sRow.= $this->_oDisplay->getBloc('', $sStart, array('class' => 'rm_start'));
        $sRow.= $this->_oDisplay->getBloc('', $sEnd, array('class' => 'rm_end'));
        $sRow.= $this->_oDisplay->getBloc('', $sAction, array('class' => 'rm_end'));


        $sHTML.= $this->_oDisplay->getBloc('', $sRow, array('class' => 'rm_row'));
        $bRead = $oDbResult->readNext();
      }
      $sHTML.= $this->_oDisplay->getBloc('', '&nbsp;', array('class' => 'rm_row'));
      $sHTML.= $this->_oDisplay->getFloatHack();
      $sHTML.= $this->_oDisplay->getBlocEnd();
      return array('data' => $sHTML);

    }


    private function _cancelCandidateRm($pnCandidatePk)
    {
      if(!assert('is_key($pnCandidatePk)'))
        return array('error' => 'Sorry, an error occured.');

      $nLoginFk = (int)getValue('loginfk', 0);
      if(!is_key($nLoginFk))
         return array('error' => 'Sorry, an error occured.');


      //check if not already RM
      $sWhere = 'candidatefk = '.$pnCandidatePk.' AND loginfk = '.$nLoginFk.' AND date_expired IS NULL ';
      $asData = array('date_expired' => date('Y-m-d H:i:s'));

      $bUpdated = $this->_getModel()->update($asData, 'sl_candidate_rm', $sWhere);
      if($bUpdated)
        return array('notice' => 'Rm candelled', 'action' => 'goPopup.removeByType(\'layer\'); $(\'#rm_link_id\').click(); ');

      return array('error' => 'Sorry, an error occured.');
    }


    private function _addCandidateRm($pnCandidatePk)
    {
      if(!assert('is_key($pnCandidatePk)'))
        return array('error' => 'Sorry, an error occured.');

      $oLogin = CDependency::getCpLogin();
      $nCurrentUserPk = $oLogin->getUserPk();

      //check if not already RM
      $sWhere = 'candidatefk = '.$pnCandidatePk.' AND loginfk = '.$nCurrentUserPk.' AND date_expired IS NULL ';
      $oDbResult = $this->_getModel()->getByWhere('sl_candidate_rm', $sWhere);
      $bRead = $oDbResult->readFirst();
      if($bRead)
        return array('message' => 'You are already RM for this candidate.');


      //add current user as RM
      $asData = array('loginfk' =>  $oLogin->getUserPk(), 'candidatefk' => $pnCandidatePk, 'date_started' => date('Y-m-d H:i:s'),
          'date_ended' => date('Y-m-d', strtotime('+3 month')).' 00:00:00') ;

      $nPk = $this->_getModel()->add($asData, 'sl_candidate_rm');
      if($nPk > 0)
        return array('notice' => 'You have been added as RM.', 'action' => 'goPopup.removeByType(\'layer\'); $(\'#rm_link_id\').click();');

      return array('error' => 'Sorry, an error occured.');
    }

    private function _extendCandidateRm($pnCandidatePk)
    {
      if(!assert('is_key($pnCandidatePk)'))
        return array('error' => 'Sorry, an error occured.');

      $nLoginFk = (int)getValue('loginfk', 0);
      if(!is_key($nLoginFk))
         return array('error' => 'Sorry, an error occured.');

      //check if not already RM
      $sWhere = 'candidatefk = '.$pnCandidatePk.' AND loginfk = '.$nLoginFk.' AND date_expired IS NULL ';
      $asData = array('date_ended' => date('Y-m-d H:i:s', strtotime('+3 month')), 'nb_extended' => 'nb_extended+1');

      $bUpdated = $this->_getModel()->update($asData, 'sl_candidate_rm', $sWhere);
      if($bUpdated)
        return array('notice' => 'Rm renewd. <br/>Following the candidate until '.date('d M Y', strtotime('+3 month')), 'action' => 'goPopup.removeByType(\'layer\'); $(\'#rm_link_id\').click(); ');

      return array('error' => 'Sorry, an error occured.');
    }


    private function _manageRmExpiration($pbForce = false)
    {
      $oSetting = CDependency::getComponentByName('settings');
      $asSetting = $oSetting->getSystemSettings('notify_rm');

      $nWeek = date('W');
      if($pbForce || empty($asSetting['notify_rm']) || $asSetting['notify_rm'] < $nWeek)
      {
        //fetch expiring RMs
        $sNow = date('Y-m-d').' 00:00:00';
        $sExpireDate = date('Y-m-d', strtotime('+15 days'));

        $sQuery = 'SELECT scrm.*, slog.*, CONCAT(scan.firstname, " ", scan.lastname) as candidate  FROM sl_candidate_rm as scrm
          INNER JOIN sl_candidate as scan ON (scan.sl_candidatepk = scrm.candidatefk)
          INNER JOIN shared_login as slog ON (slog.loginpk = scrm.loginfk)
          WHERE (scrm.date_expired IS NULL OR scrm.date_expired = "")
          AND scrm.date_ended <= "'.$sExpireDate.'"
            ';  // AND slog.status > 0

        $oDbResult = $this->_getModel()->executeQuery($sQuery);
        $bRead = $oDbResult->readFirst();
        if(!$bRead)
        {
          echo 'CRON RM notification - no rm notfication to send';
        }
        else
        {
          $asUserNotification = array();
          $anExpired = array();
          $asUserData = array();
          while($bRead)
          {
            $nLoginFk = (int)$oDbResult->getFieldValue('loginfk');
            $nUserStatus = (int)$oDbResult->getFieldValue('status');

            if($nUserStatus > 0)
            {
              $asUserData[$nLoginFk]['email'] = $oDbResult->getFieldValue('email');
              $asUserData[$nLoginFk]['name'] = $oDbResult->getFieldValue('firstname').' '.$oDbResult->getFieldValue('lastname');
              $asUserData[$nLoginFk]['firstname'] = $oDbResult->getFieldValue('firstname');

              $nCandidatefk = (int)$oDbResult->getFieldValue('candidatefk');
              $sURL = $this->_oPage->getUrl('sl_candidate', CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, $nCandidatefk);
              $sDate = substr($oDbResult->getFieldValue('date_ended'), 0, 10);
              $asUserNotification[$nLoginFk][$nCandidatefk] = 'expires on the '.$sDate.' for candidate <a href="'.$sURL.'">#'.$nCandidatefk.' - '.$oDbResult->getFieldValue('candidate').'</a>';
            }

            //set expired all reminders for inactive users and the ones which really expired this today
            if($nUserStatus == 0 || $oDbResult->getFieldValue('date_ended') < $sNow)
              $anExpired[] = (int)$oDbResult->getFieldValue('sl_candidate_rmpk');

            $bRead = $oDbResult->readNext();
          }

          $oMail = CDependency::getComponentByName('mail');
          foreach($asUserNotification as $nUser => $asRows)
          {
            $oMail->createNewEmail();
            $oMail->setFrom(CONST_PHPMAILER_EMAIL, CONST_PHPMAILER_DEFAULT_FROM);

            $oMail->addRecipient($asUserData[$nUser]['email'], $asUserData[$nUser]['name']);

            $sSubject = 'Sl[i]stem - RM expiration';
            $sContent = 'Dear '.$asUserData[$nUser]['firstname'].', <br /><br />
              Your RM status will expire soon for '.count($asRows).' candidate(s) you are following. See below the details.<br /><br />
              <div style="border: 1px solid #ddd; margin: 5px 10px; padding: 15px; line-height: 20px;">'.implode('<br />', $asRows).'</div>';

            $oMail->send($sSubject, $sContent);
          }


          if(!empty($anExpired))
          {
            dump('rm exipiring now or user inactive:<br /> '. implode(',', $anExpired));
            $asUpdate = array('date_expired' => date('Y-m-d H:i:s'));
            $this->_getModel()->update($asUpdate, 'sl_candidate_rm', 'sl_candidate_rmpk IN ('.implode(',', $anExpired).')');
          }
        }

        $oSetting->setSystemSettings('notify_rm', $nWeek);
      }
      else
        echo 'CRON RM notification already sent today';

      return true;
    }

    private function _getMergeForm($pnCandidatePk)
    {
      if(!assert('is_key($pnCandidatePk)'))
        return array('error' => 'Wrong parameters');

      $nManualTarget = (int)getValue('target');

      $sHTML = $this->_oDisplay->getTitle('Duplicates for candidate #'.$pnCandidatePk, 'h3', true);
      $sHTML.= $this->_oDisplay->getCR(2);


      $duplicate_array = $this->_getModel()->getDuplicate($pnCandidatePk, $nManualTarget, true, true);

      if(empty($duplicate_array['other']))
      {
        $sHTML.= '<span style="font-size: 15px; color: green; ">&rarr; No duplicate found for this candidate.</span><br /><br />';
      }
      else
      {
        foreach ($duplicate_array['other'] as $key => $value)
        {
          if ($key == $pnCandidatePk)
          {
            unset($duplicate_array['other'][$key]);
            continue;
          }

          $sURL = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, $pnCandidatePk);
          $sHTML.= '#<a href="javascript:;"  onclick="popup_candi(this, \''.$sURL.'\');" >'.$value['sl_candidatepk'].' - '.$value['lastname'].' '.$value['firstname'].'</a><br />';
          $sHTML.= 'Working at '.$value['company'].'';

          $sURL = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_TRANSFER, CONST_CANDIDATE_TYPE_CANDI, $pnCandidatePk, array('merge_to' => $value['sl_candidatepk']));
          $sHTML.= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <a href="javascript:;" onclick="if(window.confirm(\'Are you sure you want to merge #'.$pnCandidatePk.' data to this profile (#'.$value['sl_candidatepk'].') ?\'))
            {
              AjaxRequest(\''.$sURL.'\');
            }
            ">-=[ Merge on this candidate profile ]=-</a><br /><br />';
        }
      }

      $sHTML.= $this->_oDisplay->getCR(1);

      $sURL = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_MANAGE, CONST_CANDIDATE_TYPE_CANDI, $pnCandidatePk);
      $sHTML.= 'Looking for a specific duplicate ? You can manually input a refId here: <br /><br />
        <input type="text" id="lookForDup" />&nbsp;&nbsp;
        <a href="javascript:;" onclick="
        var nRefId = $(\'#lookForDup\').val();
        if(nRefId.trim().length < 2)
          return alert(\'RefId is wrong\');

        goPopup.removeLastByType(\'layer\');

        var oConf = goPopup.getConfig();
        oConf.width = 1080;
        oConf.height = 725;
        goPopup.setLayerFromAjax(oConf, \''.$sURL.'&target=\'+nRefId);

        " > >> Search</a>';

      $sHTML.= $this->_oDisplay->getCR(4);
      $sHTML.= $this->_oDisplay->getTitle('Delete candidate', 'h3', true);

      $sURL = $this->_oPage->getAjaxUrl($this->csUid, CONST_ACTION_TRANSFER, CONST_CANDIDATE_TYPE_CANDI, $pnCandidatePk);
      $sHTML.= '<div style="font-size: 15px;">
        <br />No duplicates, empty or useless profile... <br />
        <a href="javascript:;" style="font-size: 15px;"
        onclick="
        if(window.confirm(\'Are you sure you want to delete this candidate ?\'))
        {
          AjaxRequest(\''.$sURL.'\');
        }
        ">Do you want to <span style="color: #A72A19; font-size: 15px;">delete this candidate</span> ?</a>
        </div>';

      return array('data' => $sHTML);
    }


    private function _mergeDeleteCandidate($candidate_id)
    {

      if(!assert('is_key($candidate_id)'))
        return array('error' => __LINE__.' - Wrong parameters');

      $asCandidate = $this->_getModel()->getCandidateData($candidate_id, true);
      if(empty($asCandidate))
        return array('error' => __LINE__.' - Could not find the candidate.');


      $target_candidate_id = (int)getValue('merge_to');

      // - -- - - - -- - -- - - - -- - -- - - - -- - -- - - - -- - -- - - - -- - -- - - - --
      // - -- - - - -- - -- - - - -- - -- - - - -- - -- - - - -- - -- - - - -- - -- - - - --
      //merge with nothing ==> simple delete

      if(empty($target_candidate_id))
      {
        $asData = array('_sys_status' => 1, '_sys_redirect' => NULL, '_date_updated' => date('Y-md H:i:s'));
        $this->_getModel()->update($asData, 'sl_candidate', 'sl_candidatepk = '.$candidate_id);

        $sUrl = $this->_oPage->getAjaxUrl('555-001', CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, $candidate_id);
        return array('notice' => 'Candidate has been deleted.', 'action' => 'goPopup.removeLastByType(\'layer\'); view_candi(\''.$sUrl.'\');');
      }


      // - -- - - - -- - -- - - - -- - -- - - - -- - -- - - - -- - -- - - - -- - -- - - - --
      // - -- - - - -- - -- - - - -- - -- - - - -- - -- - - - -- - -- - - - -- - -- - - - --
      //move all data across


      //load a genric model to update multi component
      $model_object = new CModel(true);
      $summary = array();

      //1. merge profile data
      $adjusted_candidate_ids = $this->merge_candidate_profiles($candidate_id, $target_candidate_id);

      /*$asTarget = $this->_getModel()->getCandidateData($adjusted_candidate_ids['target'], true);
      if(empty($asTarget))
        return array('error' => __LINE__.' - Could not find the target candidate.');*/

      // Swap ids if necessary for preserving older candidate info
      $target_candidate_id = $adjusted_candidate_ids['target'];
      $candidate_id = $adjusted_candidate_ids['origin'];

      //2. move reminders / dba req / notifications
      $asData = array('cp_pk' => $target_candidate_id);
      $oDbResult = $model_object->update($asData, 'notification_link', 'cp_uid = "555-001" AND cp_action = "ppav" AND cp_type = "candi" AND cp_pk = '.$candidate_id, true);
      $summary['reminders'] = $oDbResult->getFieldValue('_affected_rows');

      //3. move meetings
      $asData = array('candidatefk' => $target_candidate_id);
      $oDbResult = $model_object->update($asData, 'sl_meeting', 'candidatefk = '.$candidate_id, true);
      $summary['meetings'] = $oDbResult->getFieldValue('_affected_rows');

      //4.1 move positions_link (for history)
      $asData = array('candidatefk' => $target_candidate_id);
      $oDbResult = $model_object->update($asData, 'sl_position_link', 'candidatefk = '.$candidate_id, true);
      $summary['positions'] = $oDbResult->getFieldValue('_affected_rows');

      //4.2 move sl_position_credit (for history and placement manager)
      $asData = array('candidatefk' => $target_candidate_id);
      $oDbResult = $model_object->update($asData, 'sl_position_credit', 'candidatefk = '.$candidate_id, true);
      $summary['position_credit'] = $oDbResult->getFieldValue('_affected_rows');

      //5. move revenue (for placement manager)
      $asData = array('candidate' => $target_candidate_id);
      $oDbResult = $model_object->update($asData, 'revenue', 'candidate = '.$candidate_id, true);
      $summary['revenue'] = $oDbResult->getFieldValue('_affected_rows');

      //6. documents
      $asData = array('cp_pk' => $target_candidate_id);
      $oDbResult = $model_object->update($asData, 'document_link', 'cp_uid = "555-001" AND cp_action = "ppav" AND cp_type = "candi" AND cp_pk = '.$candidate_id, true);
      $summary['documents'] = $oDbResult->getFieldValue('_affected_rows');

      //7. contact
      $asData = array('itemfk' => $target_candidate_id);
      $oDbResult = $model_object->update($asData, 'sl_contact', 'item_type = "candi" AND itemfk = '.$candidate_id, true);
      $summary['contacts'] = $oDbResult->getFieldValue('_affected_rows');

      //8. attribute
      $asData = array('itemfk' => $target_candidate_id);
      $oDbResult = $model_object->update($asData, 'sl_attribute', 'type LIKE "candi%" AND itemfk = '.$candidate_id, true);
      $summary['attributes'] = $oDbResult->getFieldValue('_affected_rows');

      //9. RM
      $asData = array('candidatefk' => $target_candidate_id);
      $oDbResult = $model_object->update($asData, 'sl_candidate_rm', 'candidatefk = '.$candidate_id, true);
      $summary['rm'] = $oDbResult->getFieldValue('_affected_rows');

      //10. notes
      $asData = array('cp_pk' => $target_candidate_id);
      $oDbResult = $model_object->update($asData, 'event_link', 'cp_uid = "555-001" AND cp_action = "ppav" AND cp_type = "candi" AND cp_pk = '.$candidate_id, true);
      $summary['notes'] = $oDbResult->getFieldValue('_affected_rows');

      //11. user activity
      $asData = array('cp_pk' => $target_candidate_id);
      $oDbResult = $model_object->update($asData, 'login_system_history', 'cp_uid = "555-001" AND cp_type = "candi" AND cp_pk = '.$candidate_id, true);
      $summary['activity'] = $oDbResult->getFieldValue('_affected_rows');

      //12. add note summary, copy UID
      $oEvent = CDependency::getComponentByName('sl_event');
      $note = 'The candidate #'.$candidate_id.' has been merge on this candidate profile.<br />';
      $note.= 'All data have been moved accross, previous UID : '.$asCandidate['uid'].'<br />';

      $merges = 'The candidate #'.$candidate_id.' has been merge on this candidate profile.<br />';
      $merges.= 'All data have been moved accross to : '.$target_candidate_id.'<br />';

      foreach($summary as $type => $update)
      {
        $merges.= '-> #'.$update.' '.$type.' transfered<br />'; //adayin notlarina yapilan islemle ilgili ekleme yapiyordu istemediler kaldirdik
      }
      $pasOldData['log_detail'] = '';

      $oEvent->addNote($target_candidate_id, 'merge_summary', $note);


      $asData = array('_sys_status' => 2, '_sys_redirect' => $target_candidate_id, '_date_updated' => date('Y-md H:i:s'));
      $this->_getModel()->update($asData, 'sl_candidate', 'sl_candidatepk = '.$candidate_id);

      $this->_getModel()->_logChanges($pasOldData, 'sl_candidate', $merges);

      $sUrl = $this->_oPage->getAjaxUrl('555-001', CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_CANDI, $target_candidate_id);
      return array('notice' => 'Candidate has been merged with #'.$candidate_id.'.', 'action' => 'goPopup.removeLastByType(\'layer\'); view_candi(\''.$sUrl.'\');');
    }

    private function _customLogUpdate($pasOldData, $pasNewData)
    {

      $asBLFields = array('updated_by', 'date_updated', 'sl_candidatepk', ' date_created', '_sys_status', '_sys_redirect',
          'currency', 'currency_rate', 'salary_search', ' _has_doc', '  _in_play', '  _date_updated', 'uid', 'rating');

      $asProfessional = array('companyfk' => 'company', 'industryfk' => 'industry', 'occupationfk' => 'occupation',
          'title' => 'title', 'department' => 'department', 'salary' => 'salary', 'bonus' => 'bonus', 'target_low' => 'target salary from'
          , 'target_high' => 'target salary to');

      $asStatus = array('statusfk' => 'status', 'play_for' => 'playing for', 'play_date' => 'playing date',
          'is_client' => 'is a client', 'is_collaborator' => 'is a collaborator');

      $asPersonal = array('sex' => 'gender', 'firstname' => 'firstname', 'lastname' => 'lastname',
          'nationalityfk' => 'nationality', 'languagefk' => 'language',
          'locationfk' => 'location', 'languagefk' => 'language', 'grade' => 'grade', 'keyword' => 'keywords',
          'date_birth' => 'birthday', 'is_birth_estimation' => 'birthday',
          'cpa' => 'cpa', 'mba' => 'mba', 'skill_ag' => 'AG', 'skill_ap' => 'AP', 'skill_am' => 'AM', 'skill_mp' => 'MP'
           ,'skill_in' => 'IN', 'skill_ex' => 'EX', 'skill_fx' => 'FX', 'skill_ch' => 'CH', 'skill_ed' => 'ED', 'skill_pl' => 'PL', 'skill_e' => 'e');

      $asLog = array('Business data' => array(), 'Status' => array(), 'Personal data' => array());
      foreach($pasOldData as $sField => $vValue)
      {
        //ignore black listed fields
        if(!in_array($sField, $asBLFields))
        {

          if($vValue === 'null' || $vValue == '0000-00-00 00:00:00')
            $vValue = null;

          if($pasNewData[$sField] === 'null' || $pasNewData[$sField] == '0000-00-00 00:00:00')
            $pasNewData[$sField] = null;

          //we can have different version of empty 0, null, ''
          if( (empty($vValue) && empty($pasNewData[$sField])) || $vValue == $pasNewData[$sField])
          {
            //nothing to do
          }
          else
          {
            if(isset($asProfessional[$sField]))
            {
              $sType = 'Business data';
              $sLabel = $asProfessional[$sField];
            }
            elseif(isset($asStatus[$sField]))
            {
              $sType = 'Status';
              $sLabel = $asStatus[$sField];
            }
            else
            {
              $sType = 'Personal data';
              if(isset($asPersonal[$sField]))
                $sLabel = $asPersonal[$sField];
              else
                $sLabel = ' - ';
            }

            if (empty($this->coSlateVars))
              $this->getVars();

            if(empty($vValue) && !empty($pasNewData[$sField]))
            {
              $added_variable = $this->coSlateVars->get_var_info_by_label($sLabel, $pasNewData[$sField]);

              if (empty($added_variable))
                $added_variable = $pasNewData[$sField];
              if(!empty($added_variable) && $added_variable != 'NULL')
              {
                $asLog[$sType][] = '['.$sLabel.'] has been added : '.$added_variable;
              }
            }
            else
            {
              $old_variable = $this->coSlateVars->get_var_info_by_label($sLabel, $vValue);
              $new_variable = $this->coSlateVars->get_var_info_by_label($sLabel, $pasNewData[$sField]);

              if (empty($old_variable))
                $old_variable = $vValue;

              if (empty($new_variable))
                $new_variable = $pasNewData[$sField];

              if (is_array($old_variable))
                $old_variable = $old_variable['label'];

              if (is_array($new_variable))
                $new_variable = $new_variable['label'];

              if($sLabel == 'company')
              {
                $old_id = $old_variable;
                $new_id = $new_variable;

                $old_company = getCompanyInformation($old_variable);
                $new_company = getCompanyInformation($new_variable);

                $old_variable = $old_company['name']." (#".$old_id.")";
                $new_variable = $new_company['name']." (#".$new_id.")";

                $loginfk = $this->_oLogin->getUserPk();
                $cp_pk = $pasOldData['sl_candidatepk'];
                $text = '['.$sLabel.'] changed from: '.$old_variable.' -> to: '.$new_variable;

                insertLog($loginfk, $cp_pk, $text, "company_history");
                //insertEvent("company_history",$text,$loginfk,$cp_pk);
              }
              if($sLabel == 'company')
              {

              }
              if($sLabel == 'grade')
              {
                $old_variable = getGrade($pasOldData['grade']);
                $new_variable = getGrade($pasNewData['grade']);

              }

              $asLog[$sType][] = '['.$sLabel.'] changed from: '.$old_variable.' -> to: '.$new_variable;

            }
          }
        }
      }

      if(!empty($asLog))
      {
        //dump($asLog);
        $pasOldData['log_detail'] = '';
        foreach($asLog as $sType => $asLogs)
        {
          if(!empty($asLogs))
            $pasOldData['log_detail'].= '<span class="log-title">'.$sType.'</span><span class="log-desc">'.implode('<br />', $asLogs).'</span><br />';
        }


         //dump($pasOldData);
        $sTitle = 'candidate #'.$pasOldData['sl_candidatepk'].' has been updated.';
        $this->_getModel()->_logChanges($pasOldData, 'sl_candidate', $sTitle);
      }
    }

    private function merge_candidate_profiles($origin_id, $target_id)
    {
      $model_object = new CModel(true);
      $candidate_data = array();
      $false_name_array = array('mr', 'ms', 'mr.', 'ms.', 'mrs', 'mrs.');
      $newer_company_info = 'target';
      $newer_candidate_info = 'target';
      $swap = false;

      // origin cadidate info
      $query = 'SELECT sl_candidate.*, sl_position_link.date_created AS link_date,';
      $query .= ' sl_candidate_profile.date_updated';
      $query .= ' FROM sl_candidate';
      $query .= ' LEFT JOIN  sl_position_link';
      $query .= ' ON sl_position_link.candidatefk = '.$origin_id.' AND status = 101';
      $query .= ' LEFT JOIN  sl_candidate_profile';
      $query .= ' ON sl_candidate_profile.candidatefk = '.$origin_id;
      $query .= ' WHERE sl_candidate.sl_candidatepk = '.$origin_id;

      $result = $this->_getModel()->executeQuery($query);
      $read = $result->readFirst();

      $raw_data = $result->getData();

      $candidate_data['origin'] = $raw_data;

      // target cadidate info
      $query = 'SELECT sl_candidate.*, sl_position_link.date_created AS link_date,';
      $query .= ' sl_candidate_profile.date_updated';
      $query .= ' FROM sl_candidate';
      $query .= ' LEFT JOIN sl_position_link';
      $query .= ' ON sl_position_link.candidatefk = '.$target_id.' AND status = 101';
      $query .= ' LEFT JOIN sl_candidate_profile';
      $query .= ' ON sl_candidate_profile.candidatefk = '.$target_id;
      $query .= ' WHERE sl_candidate.sl_candidatepk = '.$target_id;

      $result = $this->_getModel()->executeQuery($query);
      $read = $result->readFirst();

      $raw_data = $result->getData();

      $candidate_data['target'] = $raw_data;

      $adjusted_candidate_ids = array('target' => $candidate_data['target']['sl_candidatepk'],
        'origin' => $candidate_data['origin']['sl_candidatepk']);

      if (strtotime($candidate_data['target']['date_created']) > strtotime($candidate_data['origin']['date_created']))
      {
        $adjusted_candidate_ids = array('target' => $candidate_data['origin']['sl_candidatepk'],
          'origin' => $candidate_data['target']['sl_candidatepk']);

        $temp = array();
        $temp = $candidate_data['target'];

        $candidate_data['target'] = $candidate_data['origin'];
        $candidate_data['origin'] = $temp;

        unset($temp);

        $temp = $target_id;

        $target_id = $origin_id;
        $origin_id = $temp;
      }

      if (strtotime($candidate_data['origin']['date_updated']) > strtotime($candidate_data['target']['date_updated']))
        $newer_candidate_info = 'origin';


      if (!empty($candidate_data['origin']['link_date']) || !empty($candidate_data['target']['link_date']))
      {
        if (strtotime($candidate_data['origin']['link_date']) > strtotime($candidate_data['target']['link_date']))
          $newer_company_info = 'origin';
      }
      else
        $newer_company_info = $newer_candidate_info;

      // merge sl_candidate part
      $skip_columns = array('sl_candidatepk', '_sys_status', '_sys_redirect', 'link_date',
        'date_updated', 'date_created', 'created_by');

      foreach ($candidate_data['target'] as $key => $value)
      {
        if (in_array($key, $skip_columns))
          unset($candidate_data['target'][$key]);
        else
        {
          $skip_general_overwrite = false;

          switch ($key)
          {
            case 'firstname':
              if (in_array(strtolower($candidate_data['target']['firstname']), $false_name_array))
              {
                $candidate_data['target']['firstname'] = $candidate_data['origin']['firstname'];
                $skip_general_overwrite = true;
              }
              break;

            case 'date_birth':
              if (strpos($candidate_data['target']['date_birth'], '-02-02')
                || $candidate_data['target']['date_birth'] == '0000-00-00'
                || empty($candidate_data['target']['date_birth']))
              {
                if (!empty($candidate_data['origin']['date_birth'])
                  && $candidate_data['origin']['date_birth'] != '0000-00-00')
                {
                  $candidate_data['target']['date_birth'] = $candidate_data['origin']['date_birth'];
                }
                $skip_general_overwrite = true;
              }
              else if (strpos($candidate_data['origin']['date_birth'], '-02-02')
                || $candidate_data['origin']['date_birth'] == '0000-00-00'
                || empty($candidate_data['origin']['date_birth']))
                $skip_general_overwrite = true;

              break;
          }

          if (!$skip_general_overwrite)
          {
            if (empty($candidate_data['target'][$key]) && !empty($candidate_data['origin'][$key]))
              $candidate_data['target'][$key] = $candidate_data['origin'][$key];
            else if (!empty($candidate_data[$newer_candidate_info][$key]))
              $candidate_data['target'][$key] = $candidate_data[$newer_candidate_info][$key];

            if ($key == 'is_birth_estimation'
              && (!strpos($candidate_data['target']['date_birth'], '-02-02')
              || $candidate_data['target']['date_birth'] != '0000-00-00'))
              $candidate_data['target']['is_birth_estimation'] = 0;
          }
        }
      }

     $sl_candidate_object = $model_object->update($candidate_data['target'], 'sl_candidate', 'sl_candidatepk = '.$target_id, true);

      // merge sl_candidate_profile part
      $where = "candidatefk IN ($origin_id, $target_id)";

      $result = $model_object->getByWhere('sl_candidate_profile', $where);
      $read = $result->readFirst();

      $candidate_data = array();

      while($read)
      {
        $raw_data = $result->getData();

        if ($raw_data['candidatefk'] == $origin_id)
          $candidate_data['origin'] = $raw_data;
        else if ($raw_data['candidatefk'] == $target_id)
          $candidate_data['target'] = $raw_data;

        $read = $result->readNext();
      }

      $skip_columns = array('sl_candidate_profilepk', 'candidatefk', 'uid');
      $newer_fields = array('companyfk', 'department', 'title', 'industryfk', 'occupationfk',
        'salary', 'bonus', 'salary_search', 'target_low', 'target_high');

      foreach ($candidate_data['target'] as $key => $value)
      {
        $skip_general_overwrite = false;

        if (in_array($key, $newer_fields))
        {
          if (empty($candidate_data['target'][$key]) && !empty($candidate_data['origin'][$key]))
            $candidate_data['target'][$key] = $candidate_data['origin'][$key];
          else if (!empty($candidate_data[$newer_company_info][$key]))
            $candidate_data['target'][$key] = $candidate_data[$newer_company_info][$key];

          continue;
        }

        if ($key == 'grade')
        {
          if ((int)$candidate_data['origin']['grade'] > (int)$candidate_data['target']['grade'])
          {
            $candidate_data['target']['grade'] = $candidate_data['origin']['grade'];
            $skip_general_overwrite = true;
          }
        }

        if (in_array($key, $skip_columns))
          unset($candidate_data['target'][$key]);
        else if (!$skip_general_overwrite)
        {
          if (empty($candidate_data['target'][$key]) && !empty($candidate_data['origin'][$key]))
            $candidate_data['target'][$key] = $candidate_data['origin'][$key];
          else if (!empty($candidate_data[$newer_candidate_info][$key]))
            $candidate_data['target'][$key] = $candidate_data[$newer_candidate_info][$key];
        }
      }

      $sl_candidate_profile_object = $model_object->update($candidate_data['target'], 'sl_candidate_profile', 'candidatefk = '.$target_id, true);

      $recalculated_profile_rating = $this->calculate_profile_rating($target_id);

      if($recalculated_profile_rating < $candidate_data['target']['profile_rating'])
      {
        $recalculated_profile_rating = $candidate_data['target']['profile_rating'];
      }

      if($recalculated_profile_rating < $candidate_data['origin']['profile_rating'])
      {
        $recalculated_profile_rating = $candidate_data['origin']['profile_rating'];
      }
      //$candidate_data['target']['profile_rating'] = $recalculated_profile_rating;
      //$sl_candidate_profile_object = $model_object->update($candidate_data['target'], 'sl_candidate_profile', 'candidatefk = '.$target_id, true);
      $candidate_id = $this->_getModel()->update_candidate_profile_rating($recalculated_profile_rating, $target_id);

      return $adjusted_candidate_ids;
    }

  private function _buildCandidateQuickSearch($pbStrict = true, $name = 'test')
  {
    // sort ta da buraya
    if($pbStrict)
      $sOperator = ' AND ';
    else
      $sOperator = ' OR ';

    $asTitle = array();
    $bWide = (bool)getValue('qs_wide', 0);
    $sNameFormat = getValue('qs_name_format');
    //$sSearchId = getValue('searchId');
    $sSearchId = $name;

    if($bWide)
      $sWildcard = '%';
    else
      $sWildcard = '';

    switch($sNameFormat)
    {
      case 'none':
        $sFirstField = 'lastname';
        $sSecondField = 'firstname';
        $bReverse = true;
        break;

      case 'firstname':
        $sFirstField = 'firstname';
        $sSecondField = 'lastname';
        $bReverse = false;
        break;

      case 'lastname':
      default:
        $sFirstField = 'lastname';
        $sSecondField = 'firstname';
        $bReverse = false;
        break;
    }

    //$sOperator = ' OR ';

    //if there's a ref id, no need for any other search parameter
    $sCandidate = strtolower(trim(getValue('candidate')));

    $sRefId = preg_replace('/[^0-9]/', '', $sCandidate);


    /*dump($sCandidate);
    dump($sNameFormat);
    dump($sFirstField);
    dump($sSecondField);*/

    if(!empty($sRefId) && is_numeric($sRefId))
    {
      $nRefId = (int)$sRefId;
      if($nRefId != $sRefId || $nRefId < 1)
        return 'The refId must be a positive integer.';

      $this->coQb->addWhere('sl_candidatepk = '.$nRefId);
      $asTitle[] = ' refId = '.$nRefId;
    }
    else
    {
      if(!empty($sCandidate))
      {
        //check if it's a comma separated sting
        $asWords = explode(',', $sCandidate);

        $this->_cleanArray($asWords);
        $nWord = count($asWords);
        if($nWord > 2)
          return 'Only one comma is allowed to separated the lastname and firstname.';
        /*if($nWord == 1)
        {
          $nWord = 2;
          $asWords[1] = $asWords[0];
        }*/
        //comma separated
        if($nWord == 2)
        {
          $asWords[0] = trim($asWords[0]);
          $asWords[1] = trim($asWords[1]);

          $this->coQb->addSelect(' 100-(levenshtein("'.($asWords[0].$asWords[1]).'", LOWER(CONCAT(scan.'.$sFirstField.', scan.'.$sSecondField.')))*100/LENGTH(CONCAT(scan.'.$sFirstField.', scan.'.$sSecondField.'))) AS ratio ');

          if($bReverse)
          {
            $this->coQb->addSelect(' 100-(levenshtein("'.($asWords[1].$asWords[0]).'", LOWER(CONCAT(scan.'.$sFirstField.', scan.'.$sSecondField.')))*100/LENGTH(CONCAT(scan.'.$sFirstField.', scan.'.$sSecondField.'))) AS ratio_rev ');

            $this->coQb->addWhere('( (scan.'.$sFirstField.' LIKE "'.$asWords[0].'%" '.$sOperator.' scan.'.$sSecondField.' LIKE "'.$sWildcard.$asWords[1].'%")
              OR (scan.'.$sSecondField.' LIKE "'.$sWildcard.$asWords[0].'%" '.$sOperator.' scan.'.$sFirstField.' LIKE "'.$sWildcard.$asWords[1].'%") )');

            $this->coQb->addOrder(' IF(MAX(ratio) >= MAX(ratio_rev), ratio, ratio_rev) DESC ');
          }
          else
          {
            $this->coQb->addWhere(' scan.'.$sFirstField.' LIKE "'.$sWildcard.$asWords[0].'%" '.$sOperator.' scan.'.$sSecondField.' LIKE "'.$sWildcard.$asWords[1].'%" ');

            $this->coQb->addOrder(' ratio DESC ');
          }
        }
        else
        {
          //no comma, we split the string on space
          $asWords = explode(' ', $sCandidate);
          $nWord = count($asWords);
          $this->_cleanArray($asWords);

          if($nWord == 1)
          {
            $asWords[0] = trim($asWords[0]);

            $this->coQb->addSelect(' levenshtein("'.$asWords[0].'",TRIM(LOWER(scan.lastname))) AS lastname_lev ');
            $this->coQb->addSelect(' levenshtein("'.$asWords[0].'",TRIM(LOWER(scan.firstname))) AS firstname_lev ');

            $this->coQb->addSelect(' 100-(levenshtein("'.($asWords[0]).'", LOWER(scan.'.$sFirstField.'))*100/LENGTH(scan.'.$sFirstField.')) AS ratio ');

            $this->coQb->addSelect(' 100-(levenshtein("'.($asWords[0]).'", LOWER(scan.'.$sSecondField.'))*100/LENGTH(scan.'.$sSecondField.')) AS ratio_rev ');


            $this->coQb->addWhere('( scan.lastname LIKE "'.$sWildcard.$asWords[0].'%" OR  scan.firstname LIKE "'.$sWildcard.$asWords[0].'%" ) ');

            $this->coQb->addOrder(' firstname_lev DESC ');
          }
          elseif($nWord == 2)
          {
            $asWords[0] = trim($asWords[0]);
            $asWords[1] = trim($asWords[1]);

            $this->coQb->addSelect(' 100-(levenshtein("'.($asWords[0].$asWords[1]).'", LOWER(CONCAT(TRIM(scan.'.$sFirstField.'), TRIM(scan.'.$sSecondField.'))))*100/LENGTH(CONCAT(TRIM(scan.'.$sFirstField.'), TRIM(scan.'.$sSecondField.')))) AS ratio ');

            if($bReverse)
            {
              $this->coQb->addSelect(' 100-(levenshtein("'.($asWords[1].$asWords[0]).'", LOWER(CONCAT(TRIM(scan.'.$sFirstField.'), TRIM(scan.'.$sSecondField.'))))*100/LENGTH(CONCAT(scan.'.$sFirstField.', scan.'.$sSecondField.'))) AS ratio_rev ');

              $this->coQb->addWhere('( (scan.'.$sFirstField.' LIKE "'.$sWildcard.$asWords[1].'%" '.$sOperator.' TRIM(scan.'.$sSecondField.') LIKE "'.$sWildcard.$asWords[0].'%")
              OR (TRIM(scan.'.$sSecondField.') LIKE "'.$sWildcard.$asWords[1].'%" '.$sOperator.' TRIM(scan.'.$sFirstField.') LIKE "'.$sWildcard.$asWords[0].'%") )');

              $this->coQb->addOrder(' IF(MAX(ratio) >= MAX(ratio_rev), ratio, ratio_rev) DESC ');
            }
            else
            {
              $this->coQb->addWhere(' scan.'.$sFirstField.' LIKE "'.$sWildcard.$asWords[1].'%" '.$sOperator.' scan.'.$sSecondField.' LIKE "'.$sWildcard.$asWords[0].'%" ');

              $this->coQb->addOrder(' ratio DESC ');
            }
          }
          else
          {
            foreach($asWords as $sWord)
            {
              $this->coQb->addWhere(' scan.firstname LIKE "'.$sWildcard.trim($sWord).'%" '.$sOperator.' scan.lastname LIKE "'.$sWildcard.trim($sWord).'%" ');
            }
          }
        }
        $asTitle[] = ' candidate = '.$sCandidate;
      }


      $sCompany = trim(getValue('company'));
      if($sCompany == 'Company')
        $sCompany = '';
      else
        $sCompany = strtolower($sCompany);

      if(!empty($sCompany))
      {
        $asTitle[] = ' company = '.$sCompany;

        $bXCompany = (substr($sCompany, 0, 2) == 'x-');
        if($bXCompany)
        {
          $sCompany = trim(substr($sCompany, 2));
          //$this->coQb->addJoin('left', 'event_link', 'elin', 'elin.cp_pk = scan.sl_candidatepk AND elin.cp_uid = "555-001" AND elin.cp_type = "candi" AND elin.cp_action = "ppav"');
          $this->coQb->addJoin('left', 'event', 'even', 'even.eventpk = elin.eventfk AND even.type = "cp_hidden"');

          $asWords = explode(' ', $sCompany);
          foreach($asWords as $sWord)
            $this->coQb->addWhere(' even.content LIKE "%'.$sWord.'%" ');
        }
        else
        {
          $this->coQb->addJoin('left', 'sl_company', 'scom', 'scom.sl_companypk = scpr.companyfk');

          //Try to find a refId in the search string
          $nCompanyPk = $this->_fetchRefIdFromString($sCompany);
          if((string)$nCompanyPk == $sCompany || ('#' . $nCompanyPk) == $sCompany)
          {
            $this->coQb->addWhere('scpr.companyfk = '.$nCompanyPk);
          }
          else
          {
            //Not a ref id, we treat the string as a name
            $this->coQb->addSelect(' IF(scom.name LIKE "'.$sCompany.'", 3, IF(scom.name LIKE "'.$sCompany.'%", 2, 1)) as match_order ');
            $this->coQb->addOrder('match_order DESC, scan.sl_candidatepk');

            $asWords = explode(' ', $sCompany);
            foreach($asWords as $sWord)
              $this->coQb->addWhere(' scom.name LIKE "%'.$sWord.'%" ');
          }
        }
      }

      $sContact = trim(getValue('contact'));
      if($sContact == 'Contact')
        $sContact = '';

      if(!empty($sContact))
      {
        $sContact = trim(str_replace(';', '', $sContact));
        $this->coQb->addJoin('left', 'sl_contact', 'scon', 'scon.itemfk = scan.sl_candidatepk AND scon.item_type = "candi"');

        if($this->_lookLikePhone($sContact))
        {
          $sNumeric = preg_replace('/[^0-9]/', '', $sContact);
          //$this->coQb->addWhere(' scon.type IN (1,2,4,6) AND ( scon.value LIKE "'.$sContact.'%" OR  (scon.value REGEXP "[^0-9]") LIKE "'.$sNumeric.'%" )');
          $this->coQb->addWhere(' scon.type IN (1,2,4,6) AND ( scon.value LIKE "'.$sContact.'%" OR scon.value LIKE "'.$sNumeric.'%" )');
          $asTitle[] = ' phone = '.$sContact;
        }
        else
        {
          //if we find an @, and even if it's not a properly formated email adreesss we give a shot
          $nMatchEmail = $this->_lookLikeEmail($sContact);
          if($nMatchEmail == 2)
          {
            $this->coQb->addWhere(' scon.type = 5 AND scon.value LIKE "'.$sContact.'" ');
            $asTitle[] = ' email = '.$sContact;
          }
          elseif($nMatchEmail == 1)
          {
            $this->coQb->addWhere(' scon.type = 5 AND scon.value LIKE "%'.$sContact.'%" ');
            $asTitle[] = ' email = '.$sContact;
          }
          else
          {
            if($this->_lookLikeUrl($sContact))
            {
              $this->coQb->addWhere(' scon.type IN(3,7,8) AND scon.value LIKE "'.$sContact.'%" ');
              $asTitle[] = ' url = '.$sContact;
            }
            else
            {
              $this->coQb->addWhere(' scon.value LIKE "'.$sContact.'%" ');
              $asTitle[] = ' contact = '.$sContact;
            }
          }
        }
      }

      $sDepartment = trim(getValue('department'));
      if($sDepartment == 'Department')
        $sDepartment = '';

      if(!empty($sDepartment))
      {
        if($sDepartment == '__no_department__')
        {
          $this->coQb->addWhere(' (scpr.department IS NULL OR scpr.department = "") ');
          $asTitle[] = ' department is empty';
        }
        else
        {
          $bExactMatch = (bool)getValue('qs_exact_match', 0);
          if($bExactMatch)
            $this->coQb->addWhere(' scpr.department LIKE "'.$sDepartment.'" ');
          else
            $this->coQb->addWhere(' scpr.department LIKE "'.$sDepartment.'%" ');

          $asTitle[] = ' department = '.$sDepartment;
        }
      }

//---------------------Keyword Search Starts---------------------------

  $sKeyword = trim(getValue('keyword'));
      if($sKeyword == 'Keyword')
        $sKeyword = '';

      if(!empty($sKeyword))
      {
        if($sKeyword == '__no_keyword__')
        {
          $this->coQb->addWhere(' (scpr.keyword IS NULL OR scpr.keyword = "") ');
          $asTitle[] = ' keyword is empty';
        }
        else
        {
          $asWords = explode(',', $sKeyword);
            foreach($asWords as $sWord)
              $this->coQb->addWhere(' scpr.keyword LIKE "%'.$sWord.'%" ');

          //$sKeyword = explode(",", $sKeyword); // , ile multi search

          /*foreach ($sKeyword as $key => $value) {
            # code...
            $bExactMatch = (bool)getValue('qs_exact_match', 0);
            if($bExactMatch)
              $this->coQb->addWhere(' scpr.keyword LIKE "'.$sKeyword.'" ');
            else
              $this->coQb->addWhere(' scpr.keyword LIKE "'.$sKeyword.'%" ');
          }*/
          $asTitle[] = ' keyword = '.$sKeyword;
        }
      }

//---------------------Keyword Search ENDS-------------------------


      $sPosition = trim(getValue('position'));
      if($sPosition == 'Position ID or title')
        $sPosition = '';

      if(!empty($sPosition))
      {
        $nPositionPk = (int)$this->_fetchRefIdFromString($sPosition);

        if(!empty($nPositionPk))
        {
          $this->coQb->addJoin('inner', 'sl_position_link', 'spli', ' spli.candidatefk = scan.sl_candidatepk AND spli.active = 1 AND spli.positionfk = "'.$nPositionPk.'" ');
          $asTitle[] = ' position ID = #'.$nPositionPk;
        }
        else
        {

          $sCleanPosition = addslashes($sPosition);

          $this->coQb->addJoin('inner', 'sl_position_link', 'spli', ' spli.candidatefk = scan.sl_candidatepk AND spli.active = 1');
          $this->coQb->addJoin('inner', 'sl_position_detail', 'spde', ' spde.positionfk = spli.positionfk
            AND (spde.title LIKE "%'.$sCleanPosition.'%" OR spde.description LIKE "%'.$sCleanPosition.'%" ) ');

          $asTitle[] = ' position = '.$sPosition;
        }

        $sStatus = getValue('position_status');
        if(!empty($sStatus))
        {
          $sStart = substr($sStatus, 0, 1);
          if($sStart == '+')
          {
            $this->coQb->addWhere(' spli.status >= '.(int)substr($sStatus, 1) );
          }
          elseif($sStart == '-')
          {
            $this->coQb->addWhere(' spli.status <= '.(int)substr($sStatus, 1) );
          }
          else
           $this->coQb->addWhere(' spli.status = '.(int)$sStatus);
        }
      }
    }

    //if search Id, i may just be filtering or sorting the results... no need to check params
    if(empty($sKeyword) && empty($sSearchId) && empty($sRefId) && empty($sCandidate) && empty($sContact) && empty($sDepartment) && empty($sCompany) && empty($sPosition))
    {
      return 'You need to input a refId, a name, a contact detail, a company or a keyword.'.' kw:'.$sKeyword;
    }

    $this->coQb->setTitle('QuickSearch: '.implode(' , ', $asTitle));

    return '';
  }
}