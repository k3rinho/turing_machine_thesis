<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use App\Models\Machine;
use App\Models\Task;

class MachineController extends Controller
{

  // példányváltozókat itt hozzuk létre, a lenti függvénykben globálisan elérhetőek
  public $cursorIndex = 0;

  // ha nincs input megadva api hívásokkor, az alábbit használjuk
  public $input = "#1110#";

  // rules-ba jön a teljes szabályrendszer
  public $rules;

  // zárószabály neve mindig h
  public $endState = "h";

  // az outputot ide generáljuk, egy arraybe lesz tárolva step-by-step
  public $output = [];
  public $activeMachine = 0;
  public $nextState = 0;
  public $isNew = false;
  public $allRUles = [];
  public $prevReturnState = "";
  public $prevReturnID = 0;

  public function __construct(Request $request)
  {
    // a construct függvény mindig meghívódik első lépésként minden API hívásnál
    // esetünkben az első lépés, hogy bekérjük a szabályokat a frontendről ($request->data)
    $this->rules = $request->data;

    // need to stringify, then prepare a NON-ASSOCIATIVE ARRAY, we need OBJECT instead
    $this->allRules = json_decode(json_encode($this->rules),false);
    if ($request->input){
      $this->input = $request->input;
    }
  }


  // az alábbi függvény végzi a gépfuttatást
  public function processing($new = false, $cnt = 0)
  {
    // $start = "s";
    // $inputOriginal = $this->input;
    // load json file from resources/js/static

    // $cnt = 0;
    $attrs = null;
    $this->nextstate = 's';

    // $prevMachine = 0;
    // egy while ciklust addig futtatunk, amíg el nem érjük a zárószabályt
    while ($this->nextstate !== $this->endState) {

      // ac activemachine mindig az aktuális gép indexét jelöli, a lenti sor kiszedi az aktuális szabály teljes tartalmát
      // pl. név, jobbra/balra mozgás, új érték, stb.
      $this->rules = $this->allRules[$this->activeMachine]->config;

      // a nextstate a soron következő (de egészen pontosan aktuális state-et határozza meg, innen tovább haladva
      // tudjuk meg, hogy a gép következő lépése mi legyen)
      // getstate függvény megtalálható lejjebb
      $state = $this->getState($this->nextstate);

      // meghatározzuk, hogy az adott state mely celláját kell érvényesíteni (pl. S state '#' karakteréhez)
      $attrs = $this->getCharRules($state,$this->charAtCursor());

      // itt kiszedjük a soron következő state-et
      $switchState = $this->getCharRules($state,$this->charAtCursor())->uj_allapot;
      
      // az $attrs tartalmazza az aktuális lépésünk minden infóját mostmár (jobbra/balra, új karakter, stb..)
      // ezt átadjuk a state-tel a doAction függvénynek, ami végrehajtja a lépést 
      $this->doAction($this->nextstate, $attrs);
      
      // ha nem lépünk át új gépre, átadjuk a következő state-et, és megyünk tovább
      // az isNew változó a doAcionben kap értéket - ha olyal lépéshez ér, ami gépváltást eredményez, true lesz
      if (!$this->isNew){
        $this->nextstate = $switchState;
      }else{

        // ha ÚJ GÉPRE lépünk...
        // S a kezdő state (ha meg van adva hívásból, akkor az érvényesül)
        $this->nextstate = "s";

        // reseteljük az isNew változót
        $this->isNew = false;

        // rekurzívan újrahívjuk a függvényt, ami eredménye, hogy az 'új gép' lefutása után 
        // folytatja a 'szülő' gép futtatását
        $this->processing($new = false, $cnt);

        // eltároljuk, a szülő gép azonosítóját, hogy könnyen tudjuk folytatni
        $this->activeMachine = $this->prevReturnID;
        $this->nextstate = $this->prevReturnState;
      }

      // számoljuk a lépések számát
      $cnt++;
      // if ($cnt > 100){
      //   break;
      // }
    }

    // mivel a whileban az a feltételünk, hogy addig fusson a fenti ciklus, míg el nem érjük a zárószabályt (h)
    // még egyszer utoljára le kell futtatnink a doActions, hogy az utolsó lépés is lefusson
    $this->doAction($this->nextstate, $attrs);
    
    // az egész végén kiköpjük az outputot
    return $this->output;
  }

  public function doAction($state, $attrs){

    // az outputba beledobjuk a lépésünk adatait
    $this->output[]=array("machine"=> $this->activeMachine,"cursor"=>$this->cursorIndex, "input"=>$this->input, "state"=>$state, "attrs"=>$attrs);

    // a lentiekkel már a következő lépést készítjük elő!
    // ha gépváltás van, az alábbi változókat populáljuk
    // lementjük a visszatéréshez szükséges új állapotot, a szülő gép ID-ját
    // isNew-t true-ra állítjuk
    if (isset($attrs->machine) && $attrs->machine === true) {
      $this->prevReturnState = $attrs->uj_allapot;
      $this->prevReturnID = $this->activeMachine;
      $this->activeMachine = $attrs->machineID-1;
      $this->nextstate = 's';
      $this->isNew = true;
    }
    
    // ha jobbra/balra mozgás van, akkor a kurzor indexét átadjuk 
    // (az irány vagy -1 vagy 1, így tudunk negatív / pozitív irányba mozdulni)
    if (isset($attrs->irany)){
      $this->cursorIndex = $this->cursorIndex + $attrs->irany;
    }

    // ha új karaktert adunk, akkor az adott kurzotindexen lévő értéket beírjuk az 'input'ba
    // mindig az input változóból dolgozik a gép, és ezt is változtatjuk, az egyszerűség kedvéért
    // tehát az input a gépek lefutása után már jelentésben nem is input, hanem output lesz
    if (isset($attrs->uj_ertek)){
      $this->input[strlen($this->input) - $this->cursorIndex - 1] = $attrs->uj_ertek;
    }
  }

  // visszaadjuk az input változóból a kurzorindexen lévő értéket
  public function charAtCursor()
  {
    return $this->input[strlen($this->input) - $this->cursorIndex - 1];
  }

  // a state nevének megadásával (pl: s,w,l,..) kiszedjük a state-et
  public function getState($s)
  {
    $found = false;
    //iterate, check key
    foreach ($this->rules as $t => $rule) {
      if ($t == $s) {
        $found = true;
        return $rule;
      }
    }
    if (!$found && $s !== $this->endState) throw new Exception("No rule found");
  }

  // ezzel pedig az adott statehez tartozó összes karakter szabályát szerjük ki
  public function getCharRules($arr, $s)
  {
    $found = false;
    //iterate, check key
    foreach ($arr as $t => $rule) {
      if ($t == $s) {
        $found = true;
        return $rule;
      }
    }
    if (!$found) throw new Exception("No referring char found");
  }

  // SQL lekérés, adott ID-jú géphez tartozó teljes configot adja vissza
  // ahogy látszik is, a user_ID-t nem vizsgáljuk, ezzel lehetővé téve a publikus gép funkciót
  // (tehát egy gépet nemcsak a gazdája láthat kizárólag)
  public function getConfig(Request $request)
  {
    $config = Machine::where([
      ['id', '=', $request->id]
      // ['user_id', '=', auth()->user()->id]
    ])->with("task")->first();

    return $config;
  }

  // menti a változtatásokat
  public function saveConfig(Request $request)
  {
    $config = Machine::where([
      ['id', '=', $request->id],
      ['user_id', '=', auth()->user()->id]
    ])->update([
      'data' => $request->data
    ]);

    return $config;
  }

  // projektet / konfigurációt hozunk létre, az aktív user ID-jával
  public function createproject(Request $request)
  {
    $task = $request->get("task");
    
    $config = Machine::create([
      'user_id' => auth()->user()->id,
      'name' => $request->name,
      'task_id'=> $task,
      'data' => file_get_contents(resource_path('js/static/defaultmachine.json'))
    ]);
    return $config;
  }

  // konfig törlése
  public function deleteproject(Request $request)
  {
    $config = Machine::where([
      ['id', '=', $request->id],
      ['user_id', '=', auth()->user()->id]
    ])->delete();

    return $config;
  }
  

  // gép publikálása (oublic oszlopot 0-ról 1-re állítunk)
  public function setpublic(Request $request)
  {
    $config = Machine::where([
      ['id', '=', $request->id],
      ['user_id', '=', auth()->user()->id]
    ])->first();
    $config->public = $request->public;
    $config->save();
  }

  // task-ok listázása
  public function getTasks(Request $request)
  {
    // ha ne mutassuk a feladatot, amit már "megoldott", itt lesz a megoldás"
    // $existing_tasks = Machine::pluck('task_id')->where([
    //   'user_id', '=', auth()->user()->id
    // ])->toArray();
    $tasks = Task::with('user')->get();
    return $tasks;
  }

  // az összes gépet begyűjtjük, amik egy adott taskhoz tartoznak
  public function getMachinesByTask(Request $request)
  {
    $machines = Machine::where(
      'task_id', '=', $request->id
    )->with('user')->get();
    return $machines;
  }

  // összes task listázása 
  public function getTasksbyOwner(Request $request)
  {
    $tasks = Task::with('user')->where("creator_id", auth()->user()->id)->get();
    return $tasks;
  }
  
  // új feladat létrehozása
  public function addTask(Request $request){
    $task = Task::create($request->all());
    return $task;
  }

  // publikus gépek kigyűjtése
  public function getPublicMachines(Request $request)
  {
    // ha ne mutassuk a feladatot, amit már "megoldott", itt lesz a megoldás"
    // $existing_tasks = Machine::pluck('task_id')->where([
    //   'user_id', '=', auth()->user()->id
    // ])->toArray();
    $tasks = Machine::where([
      ['public', '=', 1],
      ['user_id', '!=', auth()->user()->id]
    ])->with("task")->get();
    return $tasks;
  }

}
