<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/component/display/resources/class/template/template.tpl.class.php5');

class CCandidate_sl3 extends CTemplate
{
  protected $coDisplay = null;

  public function __construct(&$poTplManager, $psUid, $pasParams, $pnTemplateNumber)
  {
    $this->csTplType = 'bloc';
    $this->casTplToLoad = array();
    $this->casTplToProvide = array();

    $this->coDisplay = CDependency::getCpHtml();

    $oPage = CDependency::getCpPage();
    $oPage->addCssFile('/component/sl_candidate/resources/css/sl_candidate.css');

    parent::__construct($poTplManager, $psUid, $pasParams, $pnTemplateNumber);
  }

  public function getTemplateType()
  {
    return $this->csTplType;
  }

  public function getRequiredFeatures()
  {
    return array('to_load' => $this->casTplToLoad, 'to_provide' => $this->casTplToProvide);
  }


  public function getDisplay($pasCandidateData, $pasDisplayParams)
  {
    $oCandidate = CDependency::getComponentByName('sl_candidate');

    $sExtraStatus = '';
    $asStatus = $oCandidate->getVars()->getCandidateStatusList(true);
    $asGrade = $oCandidate->getVars()->getCandidateGradeList();
    $slPositionLinkResult = $oCandidate->getVars()->getSlPositionLinkCandidate($pasCandidateData['candidatefk']);


    /*$asLocation = $oCandidate->getVars()->getLocationList();
    $asNationality = $oCandidate->getVars()->getNationalityList();
    $asLanguage = $oCandidate->getVars()->getlanguageList();*/
    if($pasCandidateData['sex'] == 1)
       $sGenderClass = 'man';
     else
       $sGenderClass = 'woman';

    $pasCandidateData['sl_candidatepk'] = (int)$pasCandidateData['sl_candidatepk'];


    $oRight = CDependency::getComponentByName('right');
    $oLogin = CDependency::getCpLogin();
    $nCurrentUser = $oLogin->getUserPk();

    $oPage = CDependency::getCpPage();
    $oPage->addCssFile('/component/sl_candidate/resources/css/slistem3.css');
    //$asItem = array('cp_uid' => '555-001', 'cp_action' => CONST_ACTION_VIEW, 'cp_type' => CONST_CANDIDATE_TYPE_CANDI, 'cp_pk' => $pasCandidateData['sl_candidatepk']);


    //if user is here with a candidate that has a redirect ==> admin (we still make sure)
    $bAdmin = $oRight->canAccess('555-001', 'adm_dba', CONST_CANDIDATE_TYPE_CANDI);
    if(!empty($pasCandidateData['_sys_redirect']) || !empty($pasCandidateData['_sys_status']))
    {
      $sAdminClass = 'view_admin';
      $sExtraStatus = '[Merged / deleted] &nbsp;&nbsp;&nbsp;';
    }
    else
      $sAdminClass = '';

    //start a data section
    $sHTML = $this->coDisplay->getBlocStart('', array('class' => 'candiTopSection '.$sAdminClass));

      $sHTML.= $this->coDisplay->getBlocStart('', array('class' => 'candi_detail_title'));
      $sHTML.=  'Personal data <span style="margin-left: 75px; line-height: 18px; color: ">refId #'.$pasCandidateData['sl_candidatepk'].'</span>';

      $sURL = $oPage->getAjaxUrl('555-001', CONST_ACTION_ADD, CONST_CANDIDATE_TYPE_CANDI, 0, array('duplicate' => $pasCandidateData['sl_candidatepk']));
      $sHTML.=  '  <span class="action">
        <a href="javascript:;" onclick="
        var oConf = goPopup.getConfig();
        oConf.width = 1080;
        oConf.height = 725;
        goPopup.setLayerFromAjax(oConf, \''.$sURL.'\'); " title="duplicate the candidate" >
          <img src="/component/sl_candidate/resources/pictures/duplicate_nobg_24.png" /></a>';

      $sURL = $oPage->getAjaxUrl('555-001', CONST_ACTION_EDIT, CONST_CANDIDATE_TYPE_CANDI, $pasCandidateData['sl_candidatepk']);
      $sHTML.=  '&nbsp;
        <a href="javascript:;" onclick="
        var oConf = goPopup.getConfig();
        oConf.width = 1080;
        oConf.height = 725;
        goPopup.setLayerFromAjax(oConf, \''.$sURL.'\'); " title="edit candidate profile" ><img src="/component/sl_candidate/resources/pictures/edit_nobg_24.png" /></a>';



      if($bAdmin && empty($pasCandidateData['_sys_status']))
      {
        $sURL = $oPage->getAjaxUrl('555-001', CONST_ACTION_MANAGE, CONST_CANDIDATE_TYPE_CANDI, $pasCandidateData['sl_candidatepk']);
        $sHTML.=  '&nbsp;
          <a href="javascript:;" onclick="
          var oConf = goPopup.getConfig();
          oConf.width = 1080;
          oConf.height = 725;
          goPopup.setLayerFromAjax(oConf, \''.$sURL.'\'); " title="Search & merge duplicates" ><img src="/component/sl_candidate/resources/pictures/delete_nobg_24.png" /></a>';
      }

      $sHTML.= '</span>' .$this->coDisplay->getFloatHack() ;
      $sHTML.= $this->coDisplay->getBlocEnd();



       if($pasCandidateData['cp_client'] || $pasCandidateData['is_client'])
       {
         $sClass = ' candi_client ';
         $sTag = '<div class="candi_status_icon important" style="position: absolute; top: 2px; right: 0; margin: 0;">client</div> ';
         $sTitle = 'Be careful, this is a client !';
       }
       else
       {
         $sClass = $sTag = $sTitle = '';
       }

       $sValue = '<span style="font-size: 12px;">'.$pasCandidateData['lastname'].'</span>&nbsp;&nbsp;<span style="font-size: 10.5px;color: #5b5b5b">'.$pasCandidateData['firstname'].'</span>';
       if($pasCandidateData['sex'] == 1)
         $sValue = '<span class="man">Mr</span>&nbsp;'.$sValue;
       else
         $sValue = '<span class="woman">Ms</span>&nbsp;'.$sValue;


       $sValue = '<span id="candi_'.$pasCandidateData['sl_candidatepk'].'" data-title="'.$pasCandidateData['sl_candidatepk'].' - '.$pasCandidateData['lastname'].' '.$pasCandidateData['firstname'].'" data-type="candi" data-ids="'.$pasCandidateData['sl_candidatepk'].'" class="list_item_draggable '.$sGenderClass.'">'.$sValue.'</span> ';
       $sJavascript = ' if(!$(this).hasClass(\'initialized\')){  initDrag(\'#candi_'.$pasCandidateData['sl_candidatepk'].'\');  $(this).addClass(\'initialized\') } ';


       $sHTML.= $this->coDisplay->getBlocStart('', array('class' => 'candi_detail_row'.$sClass, 'title' => $sTitle, 'onmouseover' => $sJavascript));
          $sHTML.= $this->coDisplay->getBloc('', 'candidate', array('class' => 'candi_detail_label'));
          $sHTML.= $this->coDisplay->getBloc('', '<span style="float: left;">'.$sValue.'</span>'.$sTag, array('class' => 'candi_detail_value', 'style' => 'position: relative;'));
        $sHTML.= $this->coDisplay->getBlocEnd();

        $sHTML.= $this->coDisplay->getBlocStart('', array('class' => 'candi_detail_row right'));
          $sHTML.= $this->coDisplay->getBloc('', 'nationality', array('class' => 'candi_detail_label'));
          $sHTML.= $this->coDisplay->getBloc('', $pasCandidateData['nationality'], array('class' => 'candi_detail_value'));
        $sHTML.= $this->coDisplay->getBlocEnd();


        if(empty($pasCandidateData['date_birth']))
          $sValue = ' - ';
        else
        {
          $sValue = DateTime::createFromFormat('Y-m-d', $pasCandidateData['date_birth'])->diff(new DateTime('now'))->y;
          if($pasCandidateData['is_birth_estimation'])
            $sValue = '~ '.$sValue.' yrs (calc. '.$pasCandidateData['date_birth'].' )';
          else
            $sValue = $pasCandidateData['date_birth'].' ( '.$sValue.' yrs )';
        }

        $sHTML.= $this->coDisplay->getBlocStart('', array('class' => 'candi_detail_row'));
          $sHTML.= $this->coDisplay->getBloc('', 'birth', array('class' => 'candi_detail_label'));
          $sHTML.= $this->coDisplay->getBloc('', $sValue, array('class' => 'candi_detail_value'));

        $sHTML.= $this->coDisplay->getBlocEnd();

        if(isset($pasCandidateData['attribute']['candi_lang']))
        {
          $pasCandidateData['language'].= '<span class="light italic"> | '.implode(', ', $pasCandidateData['attribute']['candi_lang']).'</span>';
          $pasCandidateData['language'] = '<div class="clickable"  title="'.strip_tags($pasCandidateData['language']).'" onmouseover="tp(this);">'.$pasCandidateData['language'].'</div>';
        }
        $sHTML.= $this->coDisplay->getBlocStart('', array('class' => 'candi_detail_row right'));
          $sHTML.= $this->coDisplay->getBloc('', 'language', array('class' => 'candi_detail_label'));
          $sHTML.= $this->coDisplay->getBloc('', $pasCandidateData['language'], array('class' => 'candi_detail_value'));
        $sHTML.= $this->coDisplay->getBlocEnd();

        $sHTML.= $this->coDisplay->getBlocStart('', array('class' => 'candi_detail_row last'));
          $sHTML.= $this->coDisplay->getBloc('', 'resides', array('class' => 'candi_detail_label'));
          $sHTML.= $this->coDisplay->getBloc('', $pasCandidateData['location'], array('class' => 'candi_detail_value'));
        $sHTML.= $this->coDisplay->getBlocEnd();


       


    return $sHTML;
  }

  private function _getShortenText($psString, $pnLength = 20)
  {
    if(!assert('is_key($pnLength) && $pnLength > 3'))
      return $psString;

    if(strlen($psString) <= $pnLength)
      return $psString;

    $oPage = CDependency::getCpPage();

    $sJavascript = ' setTimeout(\'setViewTooltip(); \', 750); ';
    $oPage->addCustomJs($sJavascript);

    $sLink = substr($psString, 0, ($pnLength-3)).' ';
    $sLink.= '<a href="javascript:;" onclick="$(\'#myTooltip\').tooltip(\'open\');" title="'.str_replace('"', '\'', $psString).'" class="openTooltip">
      <span class="candi_text_shorten">&nbsp;&nbsp;&nbsp;&nbsp;</span></a>';

    return $sLink;
  }


  private function _getStatusBar($pasCandidateData)
  {
    $nPlay = $pasCandidateData['_in_play'];
    $sMeeting = substr($pasCandidateData['date_meeting'], 0, 10);

    $sHTML = $this->coDisplay->getBlocStart('', array('class' => 'candi_status_bar'));

      if(!empty($sMeeting))
      {
        $oPage = CDependency::getCpPage();
        $sURL = $oPage->getAjaxUrl('sl_candidate', CONST_ACTION_VIEW, CONST_CANDIDATE_TYPE_MEETING, $pasCandidateData['sl_candidatepk']);
        $sJavascript = 'var oConf = goPopup.getConfig(); oConf.width= 800; oConf.height = 550; goPopup.setLayerFromAjax(oConf, \''.$sURL.'\');';

        if($sMeeting == date('Y-m-d'))
          $sHTML.= '<div class="candi_status_icon meeting_set" title="Next meeting is set for today !"><a href="javascript:;" onclick="'.$sJavascript.'" style="color: #FFBF51;">'.$sMeeting.'</a></div>';
        else
          $sHTML.= '<div class="candi_status_icon meeting_set" title="Next meeting is set for the '.$sMeeting.'"><a href="javascript:;" onclick="'.$sJavascript.'" >'.$sMeeting.'</a></div>';
      }

      if($nPlay > 0)
      {
        $sHTML.= '<div class="candi_status_icon in_play" title="Candidate is active for '.$nPlay.' position(s)"><a href="javascript:;" onclick="$(\'#tabLink8\').click();" >'.$nPlay.' position(s) </a></div>';
      }
      elseif($nPlay < 0)
      {
        $sHTML.= '<div class="candi_status_icon low_priority was_in_play" title="Candidate was in play in the past"><a href="javascript:;" onclick="$(\'#tabLink8\').click();" >'.$nPlay.' | '.abs($nPlay).' position(s) played</a></div>';
      }
      else
      {
        //$sHTML.= '<span class="not_in_play" title="Candidate is not in play">&nbsp;</span>';
        $sHTML.= '<div class="candi_status_icon inactive not_in_play" title="Candidate has never been in play"></div>';
      }

      $nRating = (int)$pasCandidateData['profile_rating'];
      if(empty($nRating))
        $nRating = (int)$pasCandidateData['rating'];

      if($nRating > 85)
        $sHTML.= '<div class="candi_status_icon icon_quality featured high_quality">Q: '.$nRating.'%</span>';
      elseif($nRating > 70)
        $sHTML.= '<div class="candi_status_icon icon_quality">Q: '.$nRating.'%</span>';
      else
        $sHTML.= '<div class="candi_status_icon icon_quality low_priority">Q: '.$nRating.'%</span>';

    $sHTML.= $this->coDisplay->getBlocEnd();

    return $sHTML;
  }

}