<meta name="description" content="<?=$this->sitedesc?>">
<meta name="keywords" content="<?=implode(',', $this->keywords)?>">
<link rel="canonical" href="<?=$this->canonical?>" />
<title><?=$this->title?></title>
<!-- Facebook Open Graph rész -->
<meta property="og:title" content="<?=$this->title?>" />
<meta property="og:locale" content="hu_HU" />
<meta property="og:type" content="<?=$this->ogtype?>" /><?php
if($this->publishtime) {
    ?><meta property='article:published_time' content='<?=$this->publishtime?>' /><?php
}
if($this->shareimage) {
    ?><meta property="og:image" content="<?=$this->shareimage?>"><?php
}
?><meta property="og:url" content="<?=$this->canonical?>" />
<meta property="og:site_name" content="<?=$this->ablakcim?>" />
<meta property="og:description" content="<?=$this->sitedesc?>" />
<?= $this->localCSS ?>
<?= $this->robots ?>
<?= $this->LdJSON() ?>