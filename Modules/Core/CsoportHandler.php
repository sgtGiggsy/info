<?php
 namespace Infoinventory\Modules\Core;

 use Infoinventory\Classes\FormBuilder;

 class CsoportHandler extends Page
 {
     protected array $csoportok = [];
     protected array $csoport = [];

     protected FormBuilder $form;
     protected string $viewsgyoker = __DIR__ . "/assets/csoport/views/";
     protected array $views = [
         'csoport' => 'csoport.php',
         'csoportok' => 'csoportok.php',
         'uj' => 'uj.php',
         'szerkeszt' => 'szerkeszt.php'
     ];

     public function Router(array $params) : Page
     {
         $selecteduser = null;
         $params = $this->ParseGet($params);
         if($this->selectedpage == 'csoport') {
             if(!$params['method'])
                 $this->view = $this->views['csoport'];

             if($params['method'] == 'uj') {
                 return $this->SetSelectedUj();
             }

             if(!isset($params['elemid']))
                 return new SinglePage(404, "Nem választottál ki csoportot!");

             $this->csoport = self::GetCsoport($params['elemid']);
             $this->irasjog = $this->GetIrasjog(FELHASZNALO->uid, $params['elemid']);

             if($params['method'] == 'szerkeszt') {
                 return $this->SetSelectedSzerkeszt($this->csoport, 'csoport');
             }
         } elseif ($this->selectedpage == "csoportok") {
             if(!$this->GetOlvasasjog())
                 return new SinglePage(403);

             $this->view = $this->views['csoportok'];
             $startindex = 0;
             $elemperoldal = 20;
             $this->LapozasPrepare($startindex, $elemperoldal, $params);
             $this->csoportok = $this->LapozasFinalize(self::GetCsoportList($startindex, $elemperoldal), $elemperoldal);
         }
         return $this;
     }

    public static function GetCsoport(string|int $csoportid) : ?array
    {
        return CsoportHandlerDB::GetCsoport($csoportid);
    }

     public static function GetCsoportList(int $startindex, int $elemperoldal) : array
     {
         return CsoportHandlerDB::GetCsoportList($startindex, $elemperoldal);
     }
 }