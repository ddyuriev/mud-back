<?php

namespace App\Jobs;

use App;
use App\Helpers\Debugger;
use App\Services\CharacterService;
use Exception;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;


class SaveCharacterJob extends Job/* implements ShouldQueue*/
{
    private $character;

    public function __construct($character)
    {
        $this->character = $character;
    }

    /**
     * @param CharacterService $characterService
     */
    public function handle(CharacterService $characterService)
    {
        /**/
//        Debugger::PrintToFile('----SaveCharacterJob--' . time(), $this->character);
        /**/

        $characterService->updateCharacter($this->character);

    }
}
