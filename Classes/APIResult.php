<?php

namespace Infoinventory\Classes;

use Infoinventory\Modules\Page;

/**
 * API az oldalon belüli AJAX kérések lebonyolítására.
 *
 * Nincs API kulcs, vagy bármi egyéb authorizáció a felhasználó session-jén kívül!!!
 * A meghívható metódusokat a meghívott Page (vagyis jelenleg csak a leszármazottja) objektum $apimethods attribútuma
 * határozza meg.
 * Van fallback metódushívás is, az a betöltésre kiválasztott oldal nevével egyező metódust keres.
 * A fallback metódus csak akkor van használatban, ha az $apimethods-ban SEMMI nem lett meghatározva.
 *
 * Kimenet: szabványos JSON
 *
 * Működés: Az osztály intéz minden előzetes ellenőrzést. A meghívott osztálytól egy szimpla
 * array-t vár válaszként. Ha az osztálynál valami miatt mégis elhasalt a hívás, akkor a visszaadott
 * array-ben lennie kell "message" és "status" kulcsoknak.
 */
class APIResult
{
    public int $status = 200;
    public string $message = '';
    public array|bool $data = [];
    private string $requestmethod;
    private array $request;
    private Page|bool $page;
    private array $hivas;

    public function __construct(array|null $params) {
        $this->requestmethod = $_SERVER['REQUEST_METHOD'];
        $this->request = $this->ParseGet($params);
        $this->page = $this->VerifyRequest($this->request, $this->requestmethod);
    }

    public function Router() : APIResult {
        if(!$this->page)
            return $this;

        $page = $this->page;
        $request = $this->request;
        $requestmethod = $this->requestmethod;

        if(!$request['method']) {
            $method = ucfirst($request['page']);
        }
        else {
            $method = $request['method'];
            $method = $page->apimethods[$requestmethod][$method];
        }

        $this->hivas = [$page, $method];

        return $this;
    }

    public function Execute() : ApiResult {
        if(!$this->page)
            return $this;
        $requestmethod = $this->requestmethod;
        $request = $this->request;
        $hivas = $this->hivas;

        if($requestmethod === 'POST') {
            if (!Controller::CSRFValidator() || !Controller::OriginValidator()) {
                $this->message = 'Cross-Site Request Forgery gyanú';
                $this->status = 403;
                return $this;
            }
            Controller::POSTCleaner();
        }

        if(!is_callable($hivas)) {
            $this->message = 'A kiválasztott metódus nem érhető el';
            $this->status = 405;
            return $this;
        }

        if($request['elemid'])
            $this->data = $hivas($request['elemid']) ?? [];
        else {
            $this->data = $hivas();
            if($this->data === false) {
                $this->message = 'A kiválasztott metódust nem lehet elem azonosító nélkül meghívni';
                $this->status = 400;
                $this->data = [];
                return $this;
            }
        }

        if(!is_bool($this->data) && count($this->data) == 0) {
            $this->message = 'A kiválasztott lekérdezés nem adott vissza adatot';
        }
        else
            $this->message = 'Sikeres módosítás!';

        return $this;
    }

    private function VerifyRequest(array $request, string $requestmethod) : Page | bool {
        // Üres API hívás, kiválasztott oldal nélkül
        if($request['page'] === null) {
            $this->message = 'Hibás kérés';
            $this->status = 400;
            return false;
        }

        $page = Controller::PageSelect($request['page']);
        // Van kiválasztott oldal, de nem létezik, vagy tiltott az elérése
        if($page->selectedpage == '404' || $page->selectedpage == '403') {
            $this->message = 'A kért oldal nem érhető el a számodra';
            $this->status = (int)$page->selectedpage;
            return false;
        }

        // Vannak expliciten meghatározott API metódusok, ezért a fallback megoldás tiltása
        if(!empty($page->apimethods) && !$request['method']) {
            $this->message = 'Tiltott metódushívás';
            $this->status = 405;
            return false;
        }

        // Vannak expliciten meghatározott API metódusok, de nincs a kérés metódusára meghatározott
        if(!empty($page->apimethods) && !isset($page->apimethods[$requestmethod])) {
            $this->message = 'A kérésed típusára nem létezik metódus';
            $this->status = 405;
            return false;
        }

        // Van kiválasztott visszatérési metódus, de a kérés metódusára nem létezik ilyen
        if($request['method'] && !isset($page->apimethods[$requestmethod][$request['method']])) {
            $this->message = 'A kiválasztott metódus nem létezik';
            $this->status = 404;
            return false;
        }

        // Van kiválasztott metódus, validnak is van jelezve, viszont az osztályban nincs definiálva
        if($request['method'] && !method_exists($page, $page->apimethods[$requestmethod][$request['method']])) {
            $this->message = 'A kiválasztott metódus nincs definiálva';
            $this->status = 404;
            return false;
        }

        // Fallback. Nincs kiválasztott metódus, fallback az osztály nevével megegyező metódusra, de az nem létezik
        if(!$request['method'] && !method_exists($page, $request['page'])) {
            $this->message = 'Nincs metódus, ami ki tudná szolgálni a kérésedet';
            $this->status = 404;
            return false;
        }

        // Van kiválasztott elem, de nincs jog a lekérésére
        if($request['elemid'] && !$page->GetOlvasasjog($request['elemid'])) {
            $this->message = 'A kiválasztott elem lekérdezésére nincs jogosultságod';
            $this->status = 403;
            return false;
        }

        // Van POST kérés, de vagy nem jött vele elem azonosító, vagy jött, de nem írható
        /*if($requestmethod === 'POST'
            && (!$request['elemid'] || !$page->GetIrasjog($request['elemid']))) {
            $this->message = 'A kiválasztott elem írására nincs jogosultságod';
            $this->status = 403;
            return false;
        }*/

        $this->status = 200;
        return $page;
    }

    public function Render() : bool {
        if(isset($this->data['status']))
            $this->status = $this->data['status'];
        if(isset($this->data['message']))
            $this->message = $this->data['message'];

        $response = [];
        $response['success'] = $this->status == 200;
        if($this->status == 200) {
            $response['data'] = $this->data;
            $response['meta'] = ['count' => count($this->data)];
        }
        else {
            $response['error'] = [
                'status' => $this->status,
                'message' => $this->message];
        }

        Logging::LogApiCall();

        header('Content-Type: application/json');
        http_response_code($this->status);
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function ParseGet(?array $params) : array {
        if(!$params)
            return ['page' => null];

        switch(count($params)) {
            case 1:
                return ['page' => $params[0], 'elemid' => null, 'method' => null];
            case 2:
                if(is_numeric($params[1]))
                    return ['page' => $params[0], 'elemid' => (int)$params[1], 'method' => null];
                else
                    return ['page' => $params[0], 'method' => $params[1], 'elemid' => null];
            case 3:
                if($params[1] == 'kereses')
                    return ['page' => $params[0], 'elemid' => $params[2], 'method' => $params[1]];
                return ['page' => $params[0], 'elemid' => (int)$params[1], 'method' => $params[2]];
            default:
                return ['page' => null];
        }
    }

}