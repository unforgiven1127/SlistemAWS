<?php
require_once('component/form/fields/field.class.php5');

class CTree extends CField
{
  private $casOptionData = array();

  public function __construct($psFieldName, $pasFieldParams = array())
  {
    parent::__construct($psFieldName, $pasFieldParams);
  }

  public function addOption($pasFieldParams)
  {
    if(!assert('is_array($pasFieldParams)'))
      return null;

    if(!isset($pasFieldParams['title']) || !isset($pasFieldParams['id']))
    {
      assert('false; //option need at least a title and an id');
      return null;
    }

    if(!isset($pasFieldParams['parent']))
      $pasFieldParams['parent'] = 0;

    if(isset($pasFieldParams['selected']) && !empty($pasFieldParams['selected']))
    {
      $pasFieldParams['select'] = 'true';
      unset($pasFieldParams['selected']);
    }

    if(isset($pasFieldParams['checked']) && !empty($pasFieldParams['checked']))
    {
      $pasFieldParams['select'] = 'true';
      unset($pasFieldParams['checked']);
    }

    $this->casOptionData[] = $pasFieldParams;
    return $this;
  }


  public function getDisplay()
  {
    $sHTML = '';

    if(!isset($this->casFieldParams['id']))
    {
      $this->casFieldParams['id'] = str_replace('[', '', $this->csFieldName.'Id');
      $this->casFieldParams['id'] = str_replace(']', '', $this->casFieldParams['id']);
    }

    if(!isset($this->casFieldParams['selectedLabel']))
      $this->casFieldParams['selectedLabel'] = 'item(s) selected';

    if(!isset($this->casFieldParams['defaultLabel']))
      $this->casFieldParams['defaultLabel'] = 'Select item(s)';

    $sTreeId = 'tree_'.$this->casFieldParams['id'];
    $sFullContainerId = uniqid('tree_');

    //------------------------
    //add JScontrol classes
    if(isset($this->casFieldParams['required']) && !empty($this->casFieldParams['required']))
      $this->casFieldContol['jsFieldNotEmpty'] = '';

    if(!empty($this->casFieldParams['label']) && $this->isVisible())
      $sHTML.= '<div class="formLabel">'.$this->casFieldParams['label'].'</div>';

    $sHTML.= '<div class="formField"><input type="hidden" name="'.$this->csFieldName.'" id="'.$this->casFieldParams['id'].'" />';

    $sHTML.= '<div class="fieldTree" id="'.$sFullContainerId.'">';
    $sHTML.= '<div class="fieldTreeLabel">
      <a href="javascript:;" class="fieldTreeOpenLink" onclick="
        var oPosition =  jQuery(this).offset();
        var oTreeElement = jQuery(\'#'.$sFullContainerId.'\').find(\'.fieldTreeContainer\');
        /*oTreeElement.attr(\'style\', \'display: none; top:\'+(oPosition.top+25)+\'px; left:\'+(oPosition.left-10)+\'px;\');
        oTreeElement.attr(\'style\', \'display: none; position: fixed; top: 12.5%; left:\'+(oPosition.left-10)+\'px; \');*/
        oTreeElement.attr(\'style\', \'display: none; position: absolute; top: 15px; left:-75px; \');

        oTreeElement.fadeIn();
      " emptyLabel = "'.$this->casFieldParams['defaultLabel'].'" >';

    $nOption = $this->_countSelected($this->casOptionData);

    if($nOption > 0)
      $sHTML.= $nOption.' '.$this->casFieldParams['selectedLabel'];
    else
      $sHTML.= $this->casFieldParams['defaultLabel'];

    $sHTML.= '</a></div>';
    $sHTML.= '<div class="fieldTreeContainer hidden" id="'.$sTreeId.'" ';

    foreach($this->casFieldParams as $sKey => $vValue)
    {
        $sHTML.= ' '.$sKey.'="'.$vValue.'" ';
    }

    if(!empty($this->casFieldContol))
    {
      $sHTML.= ' jsControl="';
      foreach($this->casFieldContol as $sKey => $vValue)
        $sHTML.= $sKey.'@'.$vValue.'|';

      $sHTML.= '" ';
    }

    $sHTML.= '>';
    $sHTML.= '<div class="treeContainerClose">';

    $sHTML.= '<a href="javascript:;" onclick="$(\'#'.$sTreeId.' .treeBlock\').dynatree(\'getRoot\').visit(function(node){node.select(true);}); ">';
    $sHTML.= ' All</a>';
    $sHTML.= '<a href="javascript:;" onclick="$(\'#'.$sTreeId.' .treeBlock\').dynatree(\'getRoot\').visit(function(node){node.select(false);}); ">';
    $sHTML.= ' / None </a>';

    $sHTML.= '<a href="javascript:;" class="tree_close_button" onclick="jQuery(this).closest(\'.fieldTreeContainer\').fadeOut();">';
    $sHTML.= '<div>&nbsp;</div></a></div>';

      $sHTML.= '<div class="treeContainerList">';
      $sHTML.= '<div class="treeBlock"></div>';
      $sHTML.= '</div>';
    $sHTML.= '<div class="treeContainerSave"><a href="javascript:;" onclick="
      var oContainer = jQuery(this).closest(\'.fieldTree\');
      var nSelected = oContainer.find(\'.dynatree-selected\').length;
      oContainer.find(\'.fieldTreeOpenLink\').html(nSelected+\' '.$this->casFieldParams['selectedLabel'].'\');
      oContainer.find(\'.fieldTreeContainer\').fadeOut();

      var asId = new Array;
      $(\'#'.$sTreeId.' .treeBlock\').dynatree(\'getRoot\').visit(function(node){  if(node.isSelected()){ node.expand(true); asId.push(node.data.id); }  });
      jQuery(\'#'. $this->casFieldParams['id'].'\').val(asId.join(\',\'));

      ">Save</a></div>';
    $sHTML.= '</div></div>';



    //Generate the javascript that matches the options
    $asFieldValues = array();

    $aasTree = array();
    foreach($this->casOptionData as $nKey => $asOption)
    {
      $asFieldValues[] = $asOption['id'];

      //echo '<hr /><hr />==> treat industry '.$asOption['id'].'<br />';
      if(empty($asOption['parent']) || !$this->_insertChildInTree($aasTree, $asOption))
      {
        $this->_insertNewNode($aasTree, $asOption);
        //echo 'inserNewNode ('.$asOption['id'].') cause: parent('.$asOption['parent'].') = 0 OR no parent found<br />';
      }
    }

    $sJsValues = $this->_createJsArray($aasTree, true);

    //include dynatree file
    $oPage = CDependency::getCpPage();
    //$oPage->addJsFile(array('/component/form/resources/dynatree/jquery/jquery-ui.custom.min.js'));
    $oPage->addJsFile(array('/component/form/resources/dynatree/jquery/jquery.cookie.js'));
    $oPage->addJsFile(array('/component/form/resources/dynatree/src/jquery.dynatree.min.js'));
    $oPage->addCssFile('/component/form/resources/dynatree/src/skin-vista/ui.dynatree.css');

    $sTreeJs = "
    <script type='text/javascript'>
    var oTree = $('#".$sTreeId." .treeBlock');

    oTree.dynatree({
    title: 'fieldTree',
    minExpandLevel: 1, // 1: root node is not collapsible
    imagePath: null, // Path to a folder containing icons. Defaults to 'skin/' subdirectory.
    initId: null, // Init tree structure from a <ul> element with this ID.
    initAjax: null, // Ajax options used to initialize the tree strucuture.
    autoFocus: true, // Set focus to first child, when expanding or lazy-loading.
    keyboard: true, // Support keyboard navigation.
    persist: false, // Persist expand-status to a cookie
    autoCollapse: false, // Automatically collapse all siblings, when a node is expanded.
    clickFolderMode: 3, // 1:activate, 2:expand, 3:activate and expand
    activeVisible: true, // Make sure, active nodes are visible (expanded).
    checkbox: true, // Show checkboxes.
    selectMode: 3, // 1:single, 2:multi, 3:multi-hier
    fx: { opacity: 'toggle', duration:500 }, // Animations, e.g. null or { height: 'toggle', duration: 200 }

    noLink: false, // Use <span> instead of <a> tags for all nodes

    // Low level event handlers: onEvent(dtnode, event): return false, to stop default processing
    onClick: null, // null: generate focus, expand, activate, select events.
    onDblClick: null, // (No default actions.)
    onKeydown: null, // null: generate keyboard navigation (focus, expand, activate).
    onKeypress: null, // (No default actions.)
    onFocus: null, // null: set focus to node.
    onBlur: null, // null: remove focus from node.

    // Pre-event handlers onQueryEvent(flag, dtnode): return false, to stop processing
    onQueryActivate: null, // Callback(flag, dtnode) before a node is (de)activated.
    onQuerySelect: null, // Callback(flag, dtnode) before a node is (de)selected.
    onQueryExpand: null, // Callback(flag, dtnode) before a node is expanded/collpsed.

    // High level event handlers
    onPostInit: null, // Callback(isReloading, isError) when tree was (re)loaded.
    onActivate: null, // Callback(dtnode) when a node is activated.
    onDeactivate: null, // Callback(dtnode) when a node is deactivated.
    onSelect: null, // Callback(flag, dtnode) when a node is (de)selected.
    onExpand: null, // Callback(flag, dtnode) when a node is expanded/collapsed.
    onLazyRead: null, // Callback(dtnode) when a lazy node is expanded for the first time.
    onCustomRender: null, // Callback(dtnode) before a node is rendered. Return a HTML string to override.
    onCreate: null, // Callback(dtnode, nodeSpan) after a node was rendered for the first time.
    onRender: null, // Callback(dtnode, nodeSpan) after a node was rendered.
    postProcess: null, // Callback(data, dataType) before an Ajax result is passed to dynatree.

    children: ".$sJsValues.",
    cookieId: 'dynatree', // Choose a more unique name, to allow multiple trees.
    cookie: {
        expires: null // Days or Date; null: session cookie
    },
    debugLevel: 0, // 0:quiet, 1:normal, 2:debug


    });

    var asId = new Array;
    oTree.dynatree('getRoot').visit(function(node){  if(node.isSelected()){ node.expand(true); asId.push(node.data.id); }  });
    jQuery('#". $this->casFieldParams['id']."').val(asId.join(','));

    var oContainer = $('#".$sFullContainerId."');
    var nSelected = jQuery('.dynatree-selected', oTree).length;

    $('#".$sTreeId." .fieldTreeLabel a').html(nSelected+' ".$this->casFieldParams['selectedLabel']."');

    </script>";

    $sHTML.= $sTreeJs.'</div>';
    return $sHTML;
  }

  private function _insertChildInTree(&$paasTree, $pasOption)
  {

    $vParent = $pasOption['parent'];
    $vCurrentId = $pasOption['id'];


    //echo '<br />is there a node matching my parent id  '.$vParent.' ? <br />';

    //check if there's a parent already created
    foreach($paasTree as $vKey => $vValue)
    {
      //echo "<br />if($vKey == $vParent) ";

      if($vKey == $vParent)
      {
        //echo "<br />Found my parent, insert the node in paasTree[$vKey]['child'][$vCurrentId]['def'] ";
        $paasTree[$vKey]['child'][$vCurrentId]['def'] = $pasOption;
        $paasTree[$vKey]['child'][$vCurrentId]['child'] = array();
        return true;
      }
      else
      {
        if(!empty($paasTree[$vKey]['child']))
        {
          //echo "<br />Node has child, let s try on the next level ";
          if($this->_insertChildInTree($paasTree[$vKey]['child'], $pasOption))
            return true;
        }
      }

      //echo '<hr />';
    }

    return false;
  }

  private function _insertNewNode(&$paasTree, $pasOption)
  {
    $vCurrentId = $pasOption['id'];

    $paasTree[$vCurrentId]['def'] = $pasOption;
    $paasTree[$vCurrentId]['child'] = array();

    /*echo '<hr />'; dump($paasTree);
    echo '<br />is there a node with a parent id = '.$vCurrentId.' ? ';*/

    //check if there's a parent already created
    foreach($paasTree as $vKey => $vValue)
    {

      if($vKey !== $vCurrentId)
      {
        $vNodeParent = $vValue['def']['parent'];
        $vNodeId = $vValue['def']['id'];

        //echo '<br />'.$vNodeParent.' ? ';

        if($vNodeParent === $vCurrentId)
        {
          //move this branch as a child of the new created element
          $paasTree[$vCurrentId]['child'][$vNodeId] = $paasTree[$vKey];
          unset($paasTree[$vKey]);
        }
      }
    }

    return $paasTree;
  }


  private function _createJsArray($paasTree, $pbTop = false)
  {
    if($pbTop)
      $sJs = '[';
    else
      $sJs = '';

    $asJs = array();
    foreach($paasTree as $vKey => $avData)
    {
      $sTmpJs = '{';

      foreach($avData['def'] as $vAttribute => $vAttrValue)
      {
        if($vAttribute == 'select')
          $sTmpJs.= $vAttribute.': '.$vAttrValue.', ';
        else
          $sTmpJs.= $vAttribute.': \''.$vAttrValue.'\', ';
      }

      if(!empty($avData['child']))
      {
        $sTmpJs.= 'isFolder: true, children: ['.$this->_createJsArray($avData['child']).']';
      }
      $sTmpJs.= '}';

      $asJs[] = $sTmpJs;
    }

    if($pbTop)
      $sJs.= implode(',', $asJs).']';
    else
      $sJs.= implode(',', $asJs);

    return $sJs;
  }

  /**
   *Coun the number of selected/checked node in the tree
   * @param type $pasOption
   * @return int
   */
  private function _countSelected($pasOption)
  {
    if(!assert('is_array($pasOption)'))
      return 0;

    $nCount = 0;
    foreach($pasOption as $asData)
    {
      if(isset($asData['select']) && !empty($asData['select']))
        $nCount++;
    }

    return $nCount;
  }

}






/*
 *
 * children: [
        {title: 'Agriculture', id: 1},
        {title: 'Finance', isFolder: true, id: 2,
            children: [
                {title: 'Fin - audit', id: 345},
                {title: 'Fin - brokerage', id: 4},
                {title: 'Fin - banking', id: 5,

                  children: [{title: 'banking - private', id: 6},
                             {title: 'banking - public', isFolder: true, id: 787787,
                              children: [
                                  {title: 'national', id: 8},
                                  {title: 'international', id: 999}
                              ]
                            },
                            {title: 'banking - ret', id: 10}]
                }
            ]
        },
        {title: 'IT', isFolder: true, id: 24,
            children: [
                {title: 'IT - audit', id: 3445},
                {title: 'IT - Hardware', id: 47},
                {title: 'IT - software', id: 75,

                  children: [{title: 'software - dev', id: 67878},
                             {title: 'software - support', isFolder: true, id: 78787787,
                              children: [
                                  {title: 'online', id: 878},
                                  {title: 'phone', id: 98799}
                              ]
                            },
                            {title: 'banking - ret', id: 10}]
                }
            ]
        },
        {title: 'CNS', isFolder: true, id: 11,
            children: [
                {title: 'Fin - audit', id: 12},
                {title: 'Fin - brokerage', id: 13},
                {title: 'Fin - banking', id: 154}
                      ]
       },
       {title: 'Agriculture', id: 1},
        {title: 'Finance', isFolder: true, id: 2,
            children: [
                {title: 'Fin - audit', id: 345},
                {title: 'Fin - brokerage', id: 4},
                {title: 'Fin - banking', id: 5,

                  children: [{title: 'banking - private', id: 6},
                             {title: 'banking - public', isFolder: true, id: 787787,
                              children: [
                                  {title: 'national', id: 8},
                                  {title: 'international', id: 999}
                              ]
                            },
                            {title: 'banking - ret', id: 10}]
                }
            ]
        },
        {title: 'IT', isFolder: true, id: 24,
            children: [
                {title: 'IT - audit', id: 3445},
                {title: 'IT - Hardware', id: 47},
                {title: 'IT - software', id: 75,

                  children: [{title: 'software - dev', id: 67878},
                             {title: 'software - support', isFolder: true, id: 78787787,
                              children: [
                                  {title: 'online', id: 878},
                                  {title: 'phone', id: 98799}
                              ]
                            },
                            {title: 'banking - ret', id: 10}]
                }
            ]
        },
        {title: 'CNS', isFolder: true, id: 11,
            children: [
                {title: 'Fin - audit', id: 12},
                {title: 'Fin - brokerage', id: 13},
                {title: 'Fin - banking', id: 154}
                      ]
       },
       {title: 'Agriculture', id: 1},
        {title: 'Finance', isFolder: true, id: 2,
            children: [
                {title: 'Fin - audit', id: 345},
                {title: 'Fin - brokerage', id: 4},
                {title: 'Fin - banking', id: 5,

                  children: [{title: 'banking - private', id: 6},
                             {title: 'banking - public', isFolder: true, id: 787787,
                              children: [
                                  {title: 'national', id: 8},
                                  {title: 'international', id: 999}
                              ]
                            },
                            {title: 'banking - ret', id: 10}]
                }
            ]
        },
        {title: 'IT', isFolder: true, id: 24,
            children: [
                {title: 'IT - audit', id: 3445},
                {title: 'IT - Hardware', id: 47},
                {title: 'IT - software', id: 75,

                  children: [{title: 'software - dev', id: 67878},
                             {title: 'software - support', isFolder: true, id: 78787787,
                              children: [
                                  {title: 'online', id: 878},
                                  {title: 'phone', id: 98799}
                              ]
                            },
                            {title: 'banking - ret', id: 10}]
                }
            ]
        },
        {title: 'CNS', isFolder: true, id: 11,
            children: [
                {title: 'Fin - audit', id: 12},
                {title: 'Fin - brokerage', id: 13},
                {title: 'Fin - banking', id: 154}
                      ]
       },
       {title: 'Agriculture', id: 1},
        {title: 'Finance', isFolder: true, id: 2,
            children: [
                {title: 'Fin - audit', id: 345},
                {title: 'Fin - brokerage', id: 4},
                {title: 'Fin - banking', id: 5,

                  children: [{title: 'banking - private', id: 6},
                             {title: 'banking - public', isFolder: true, id: 787787,
                              children: [
                                  {title: 'national', id: 8},
                                  {title: 'international', id: 999}
                              ]
                            },
                            {title: 'banking - ret', id: 10}]
                }
            ]
        },
        {title: 'IT', isFolder: true, id: 24,
            children: [
                {title: 'IT - audit', id: 3445},
                {title: 'IT - Hardware', id: 47},
                {title: 'IT - software', id: 75,

                  children: [{title: 'software - dev', id: 67878},
                             {title: 'software - support', isFolder: true, id: 78787787,
                              children: [
                                  {title: 'online', id: 878},
                                  {title: 'phone', id: 98799}
                              ]
                            },
                            {title: 'banking - ret', id: 10}]
                }
            ]
        },
        {title: 'CNS', isFolder: true, id: 11,
            children: [
                {title: 'Fin - audit', id: 12},
                {title: 'Fin - brokerage', id: 13},
                {title: 'Fin - banking', id: 154}
                      ]
       }
    ]
 */
?>