<?php 
require_once($_SERVER['DOCUMENT_ROOT'].'/priv/pub/issues/sections/articles/article_common.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/DbObjectArray.php');

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /priv/logout.php");
	exit;
}

$Pub = Input::get('Pub', 'int', 0);
$Issue = Input::get('Issue', 'int', 0);
$Section = Input::get('Section', 'int', 0);
$Language = Input::get('Language', 'int', 0);
$sLanguage = Input::get('sLanguage', 'int', 0, true);
$ArticleOffset = Input::get('ArtOffs', 'int', 0, true);
$ArticlesPerPage = Input::get('lpp', 'int', 10, true);

if (!Input::isValid()) {
	header("Location: /priv/logout.php");
	exit;	
}

if ($ArticleOffset < 0) {
	$ArticleOffset = 0;
}

$publicationObj =& new Publication($Pub);
if (!$publicationObj->exists()) {
	header("Location: /priv/logout.php");
	exit;	
}

$issueObj =& new Issue($Pub, $Language, $Issue);
if (!$issueObj->exists()) {
	header("Location: /priv/logout.php");
	exit;	
}

$sectionObj =& new Section($Pub, $Issue, $Language, $Section);
if (!$sectionObj->exists()) {
	header("Location: /priv/logout.php");
	exit;		
}

$languageObj =& new Language($Language);
$sLanguageObj =& new Language($sLanguage);
$allArticleLanguages =& Article::GetAllLanguages();

if ($sLanguage) {
	// Only show a specific language.
	$allArticles = Article::GetArticles($Pub, $Issue, $Section, $sLanguage, $Language,
		$ArticlesPerPage, $ArticleOffset);
	$totalArticles = count(Article::GetArticles($Pub, $Issue, $Section, $sLanguage));
	$numUniqueArticles = $totalArticles;
	$numUniqueArticlesDisplayed = count($allArticles);
} else {
	// Show articles in all languages.
	$allArticles =& Article::GetArticles($Pub, $Issue, $Section, null, $Language,
		$ArticlesPerPage, $ArticleOffset, true);
	$totalArticles = count(Article::GetArticles($Pub, $Issue, $Section, null));
	$numUniqueArticles = Article::GetNumUniqueArticles($Pub, $Issue, $Section);
	$numUniqueArticlesDisplayed = count(array_unique(DbObjectArray::GetColumn($allArticles, 'Number')));
}

$previousArticleId = 0;

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
	"http://www.w3.org/TR/REC-html40/loose.dtd">
<HTML>
<HEAD>
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<script>
	<!--
	/*
	A slightly modified version of "Break-out-of-frames script"
	By JavaScript Kit (http://javascriptkit.com)
	*/
	
	if (window != top.fmain && window != top) {
		if (top.fmenu)
			top.fmain.location.href=location.href
		else
			top.location.href=location.href
	}
	// -->
	</script>
	<META HTTP-EQUIV="Expires" CONTENT="now">
	<TITLE><?php  putGS("Articles"); ?></TITLE>
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite["website_url"] ?>/css/admin_stylesheet.css">	
</HEAD>
<BODY  BGCOLOR="WHITE" TEXT="BLACK" LINK="DARKBLUE" ALINK="RED" VLINK="DARKBLUE">

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%">
<TR>
	<!--<TD ROWSPAN="2" WIDTH="1%"><IMG SRC="/priv/img/sign_big.gif" BORDER="0"></TD>-->
	<TD style="padding-left: 10px; padding-top: 10px;">
	    <DIV STYLE="font-size: 12pt"><B><?php  putGS("Articles"); ?></B></DIV>
	    <!--<HR NOSHADE SIZE="1" COLOR="BLACK">-->
	</TD>
<!--</TR>
<TR>
-->	<TD ALIGN="RIGHT" style="padding-right: 10px; padding-top: 10px;">
		<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0">
		<TR>
			<TD><A HREF="/priv/pub/issues/sections/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Sections"); ?>"></A></TD>
			<TD><A HREF="/priv/pub/issues/sections/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>" ><B><?php  putGS("Sections");  ?></B></A></TD>
			<TD><A HREF="/priv/pub/issues/?Pub=<?php  p($Pub); ?>" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Issues"); ?>"></A></TD>
			<TD><A HREF="/priv/pub/issues/?Pub=<?php  p($Pub); ?>" ><B><?php  putGS("Issues");  ?></B></A></TD>
			<TD><A HREF="/priv/pub/" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Publications"); ?>"></A></TD>
			<TD><A HREF="/priv/pub/" ><B><?php  putGS("Publications");  ?></B></A></TD>
<!--			<TD><A HREF="/priv/home.php" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Home"); ?>"></A></TD>
			<TD><A HREF="/priv/home.php" ><B><?php  putGS("Home");  ?></B></A></TD>
			<TD><A HREF="/priv/logout.php" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Logout"); ?>"></A></TD>
			<TD><A HREF="/priv/logout.php" ><B><?php  putGS("Logout");  ?></B></A></TD>
-->		</TR>
		</TABLE>
	</TD>
</TR>
</TABLE>
<HR NOSHADE SIZE="1" COLOR="BLACK">

<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%">
<TR>
	<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php  putGS("Publication"); ?>:</TD>
	<TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php p(htmlspecialchars($publicationObj->getName())); ?></B></TD>

	<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php  putGS("Issue"); ?>:</TD>
	<TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php p($issueObj->getIssueId()); ?>. <?php  p(htmlspecialchars($issueObj->getName())); ?> (<?php p(htmlspecialchars($languageObj->getName())); ?>)</B></TD>

	<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php  putGS("Section"); ?>:</TD>
	<TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php p($sectionObj->getSectionId()); ?>. <?php  p(htmlspecialchars($sectionObj->getName())); ?></B></TD>
</TR>
</TABLE>

<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
<TR>
<?php if ($User->hasPermission('AddArticle')) { ?>
	<TD>
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
		<TR>
			<TD><A HREF="add.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Language=<?php  p($Language); ?>&Back=<?php  pencURL($REQUEST_URI); ?>" ><IMG SRC="/priv/img/tol.gif" BORDER="0"></A></TD>
			<TD><A HREF="add.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Language=<?php  p($Language); ?>&Back=<?php  pencURL($REQUEST_URI); ?>" ><B><?php  putGS("Add new article"); ?></B></A></TD>
		</TR>
		</TABLE>
	</TD>
<?php  } ?>
	<TD ALIGN="RIGHT">
		<FORM METHOD="GET" ACTION="index.php" NAME="">
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  p($Pub); ?>">
		<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<?php  p($Issue); ?>">
		<INPUT TYPE="HIDDEN" NAME="Section" VALUE="<?php  p($Section); ?>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php  p($Language); ?>">
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3" BGCOLOR="#C0D0FF">
		<TR>
			<TD><?php  putGS('Language'); ?>:</TD>
			<TD valign="middle">
				<SELECT NAME="sLanguage" class="input_select">
				<option></option>
				<?php 
				foreach ($allArticleLanguages as $languageItem) {
					echo '<OPTION value="'.$languageItem->getLanguageId().'"' ;
					if ($languageItem->getLanguageId() == $sLanguage) {
						echo " selected";
					}
					echo '>'.htmlspecialchars($languageItem->getName()).'</option>';
				} ?>
				</SELECT>
			</TD>
			<TD><INPUT TYPE="submit" NAME="Search" VALUE="<?php  putGS('Search'); ?>" class="button"></TD>
		</TR>
		</TABLE>
		</FORM>
	</TD>
</tr>
</TABLE>

<P>
<?php 
//if ($NUM_ROWS) {
if ($numUniqueArticlesDisplayed > 0) {
	$counter = 0;
	$color = 0;
?>
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" WIDTH="100%">
<TR BGCOLOR="#C0D0FF">
	<TD ALIGN="LEFT" VALIGN="TOP"  ><B><?php  putGS("Name<BR><SMALL>(click to edit)</SMALL>"); ?></B></TD>
	<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Type"); ?></B></TD>
	<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Language"); ?></B></TD>
	<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Status"); ?></B></TD>
	<?php if ($User->hasPermission('Publish')) { ?>
	<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Order"); ?></B></TD>
	<?php } ?>
	<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Images"); ?></B></TD>
	<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Preview"); ?></B></TD>
	<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Translate"); ?></B></TD>
	<?php  if ($User->hasPermission('AddArticle')) { ?>
	<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Duplicate"); ?></B></TD>
	<?php  } ?>
	<?php  if ($User->hasPermission('DeleteArticle')) { ?>
	<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Delete"); ?></B></TD>
	<?php  } ?>	
</TR>
<?php 
$uniqueArticleCounter = 0;
foreach ($allArticles as $articleObj) {
//	if ($counter++ > $ArticlesPerPage) {
//		break;
//	}
	if ($articleObj->getArticleId() != $previousArticleId) {
		$uniqueArticleCounter++;
	}
	if ($uniqueArticleCounter > $ArticlesPerPage) {
		break;
	}
	?>	
	<TR <?php  if ($color) { $color=0; ?>BGCOLOR="#D0D0B0"<?php  } else { $color=1; ?>BGCOLOR="#D0D0D0"<?php  } ?>>
		<TD <?php if ($articleObj->getArticleId() == $previousArticleId) { ?>style="padding-left: 20px;"<?php } ?>>
		<?php
		// Can the user edit the article?
		$userCanEdit = false;
		if ($User->hasPermission('ChangeArticle') || (($User->getId() == $articleObj->getUserId()) && ($articleObj->getPublished() == 'N'))) {
			$userCanEdit = true;
		}
		if ($userCanEdit) { ?>
		<A HREF="/priv/pub/issues/sections/articles/edit.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php p($articleObj->getArticleId()); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php p($articleObj->getLanguageId()); ?>"><?php } ?><?php  p(htmlspecialchars($articleObj->getTitle())); ?>&nbsp;<?php if ($userCanEdit) { ?></A><?php } ?>
		</TD>
		<TD ALIGN="RIGHT">
			<?php p(htmlspecialchars($articleObj->getType()));  ?>
		</TD>

		<TD>
			<?php
			p(htmlspecialchars($articleObj->getLanguageName())); 
			?>
		</TD>
		<TD ALIGN="CENTER">
			<?php 
			$statusLink = '<A HREF="/priv/pub/issues/sections/articles/status.php?Pub='. $Pub 
				.'&Issue='.$Issue.'&Section='.$Section.'&Article='.$articleObj->getArticleId()
				.'&Language='.$Language.'&sLanguage='.$articleObj->getLanguageId()
				.'&Back='.urlencode($REQUEST_URI).'">';
			if ($articleObj->getPublished() == "Y") { 
				$statusWord = "Published";
			}
			elseif ($articleObj->getPublished() == "N") { 
				$statusWord = "New";
			}
			elseif ($articleObj->getPublished() == "S") { 
				$statusWord = "Submitted";
			}
			$enableStatusLink = false;
			if ($User->hasPermission('Publish')) {
				$enableStatusLink = true;
			}
			elseif ($User->hasPermission('ChangeArticle') 
					&& ($articleObj->getPublished() != 'Y')) {
				$enableStatusLink = true;
			}
			elseif ( ($User->getId() == $articleObj->getUserId())
					&& ($articleObj->getPublished() == "N")) {
				$enableStatusLink = true;
			}
			if ($enableStatusLink) {
				echo $statusLink;
			}
			putGS($statusWord);
			if ($enableStatusLink) {
				echo "</a>";
			}
			?>
		</TD>
		
		<?php
		// The MOVE links  
		if ($User->hasPermission('Publish')) { 
			if (($articleObj->getArticleId() == $previousArticleId) || ($numUniqueArticles <= 1))  {
				?>
				<TD ALIGN="CENTER" valign="middle" NOWRAP></TD>
				<?
			}
			else {
				?>
				<TD ALIGN="CENTER" valign="middle" NOWRAP>
					<table cellpadding="0" cellspacing="0">
					<tr>
						<td>
							<?php if (($ArticleOffset <= 0) && ($uniqueArticleCounter == 1)) { ?>
								<img src="/priv/img/up-dis.png">
							<?php } else { ?>
								<A HREF="/priv/pub/issues/sections/articles/do_move.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php p($articleObj->getArticleId()); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php p($sLanguage); ?>&ArticleLanguage=<?php p($articleObj->getLanguageId()); ?>&move=up_rel&pos=1&ArtOffs=<?php p($ArticleOffset); ?>"><img src="/priv/img/up.png" width="20" height="20" border="0"></A>
							<?php } ?>
						</td>
						<td>
							<?php if (($uniqueArticleCounter+$ArticleOffset) >= $numUniqueArticles) { ?>
								<img src="/priv/img/down-dis.png">
							<?php } else { ?>
								<A HREF="/priv/pub/issues/sections/articles/do_move.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php p($articleObj->getArticleId()); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php p($sLanguage); ?>&ArticleLanguage=<?php p($articleObj->getLanguageId()); ?>&move=down_rel&pos=1&ArtOffs=<?php p($ArticleOffset); ?>"><img src="/priv/img/down.png" width="20" height="20" border="0"></A>
							<?php } ?>
						</td>
						<form method="GET" action="do_move.php">
						<input type="hidden" name="Pub" value="<?php p($Pub); ?>">
						<input type="hidden" name="Issue" value="<?php p($Issue); ?>">
						<input type="hidden" name="Section" value="<?php p($Section); ?>">
						<input type="hidden" name="Language" value="<?php p($Language); ?>">
						<input type="hidden" name="sLanguage" value="<?php p($sLanguage); ?>">
						<input type="hidden" name="ArticleLanguage" value="<?php p($articleObj->getLanguageId()); ?>">
						<input type="hidden" name="Article" value="<?php p($articleObj->getArticleId()); ?>">
						<input type="hidden" name="ArtOffs" value="<?php p($ArticleOffset); ?>">
						<input type="hidden" name="move" value="abs">
						<td>
							<select name="pos" onChange="this.form.submit();" class="input_select">
							<?php
							for ($j = 1; $j <= $numUniqueArticles; $j++) {
								if (($ArticleOffset + $uniqueArticleCounter) == $j) {
									echo "<option value=\"$j\" selected>$j</option>\n";
								} else {
									echo "<option value=\"$j\">$j</option>\n";
								}
							}
							?>
							</select>
						</td>
						</form>
					</tr>
					</table>
				</TD>
				<?php  
				}
		} // if user->hasPermission('publish') 
		?>
		
		<TD ALIGN="CENTER">
			<?php  if ($articleObj->getArticleId() != $previousArticleId) { ?>
			<?php if ($userCanEdit) { ?><A HREF="/priv/pub/issues/sections/articles/images/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php p($articleObj->getArticleId()); ?>&Language=<?php  p($Language);?>&sLanguage=<?php p($articleObj->getLanguageId()); ?>"><?php } ?><?php  putGS("Images"); ?><?php if ($userCanEdit) { ?></A><?php } ?>
			<?php  } else { ?>
					&nbsp;
			<?php  } ?>
		</TD>
		<TD ALIGN="CENTER">
			<A HREF="" ONCLICK="window.open('/priv/pub/issues/sections/articles/preview.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($articleObj->getArticleId()); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($articleObj->getLanguageId()); ?>', 'fpreview', 'resizable=yes, menubar=yes, toolbar=yes, width=680, height=560'); return false"><?php  putGS("Preview"); ?></A>
		</TD>
		<TD ALIGN="CENTER">
			<?php  if ($articleObj->getArticleId() != $previousArticleId) { ?>
			<A HREF="/priv/pub/issues/sections/articles/translate.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php p($articleObj->getArticleId()); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php p($articleObj->getLanguageId()); ?>&Back=<?php  pencURL($REQUEST_URI); ?>"><?php  putGS("Translate"); ?></A>
			<?php  } else { ?>
				&nbsp;
			<?php  } ?>
		</TD>
		
		<?php  if ($User->hasPermission('AddArticle')) { ?>
		<TD ALIGN="CENTER">
			<A HREF="/priv/pub/issues/sections/articles/duplicate.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php p($articleObj->getArticleId()); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($articleObj->getLanguageId()); ?>&Back=<?php p(urlencode($REQUEST_URI)); ?>"><?php  putGS("Duplicate"); ?></A>
		</TD>
		<?php  } ?>

		<?php  if ($User->hasPermission('DeleteArticle')) { ?>
		<TD ALIGN="CENTER">
			<A HREF="/priv/pub/issues/sections/articles/do_del.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php p($articleObj->getArticleId()); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php p($articleObj->getLanguageId()); ?>" onclick="return confirm('<?php htmlspecialchars(putGS('Are you sure you want to delete the article $1 ($2)?', $articleObj->getTitle(), $articleObj->getLanguageName())); ?>');"><IMG SRC="/priv/img/icon/delete.gif" BORDER="0" ALT="<?php  putGS('Delete article $1', $articleObj->getTitle()); ?>"></A>
		</TD>
		<?php  }
		if ($articleObj->getArticleId() != $previousArticleId)
			$previousArticleId = $articleObj->getArticleId();
		?>	
	</TR>
	<?php 
} // foreach
?>	
<TR>
	<TD NOWRAP>
		<?php 
    	if ($ArticleOffset > 0) { ?>
			<B><A HREF="index.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&ArtOffs=<?php  p(max(0, ($ArticleOffset - $ArticlesPerPage))); ?>">&lt;&lt; <?php  putGS('Previous'); ?></A></B>
		<?php  }

    	if ( ($ArticleOffset + $ArticlesPerPage) < $numUniqueArticles) { 
    		if ($ArticleOffset > 0) {
    			?>|<?php
    		}
    		?>
			 <B><A HREF="index.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&ArtOffs=<?php  p(min( ($numUniqueArticles-1), ($ArticleOffset + $ArticlesPerPage))); ?>"><?php  putGS('Next'); ?> &gt;&gt</A></B>
		<?php  } ?>
	</TD>
	<td colspan="3">
		<?php putGS("$1 articles found", $numUniqueArticles); ?>
	</td>
</TR>
</TABLE>
<?php  } else { ?><BLOCKQUOTE>
	<LI><?php  putGS('No articles.'); ?></LI>
	</BLOCKQUOTE>
<?php  } ?>
<?php CampsiteInterface::CopyrightNotice(); ?>