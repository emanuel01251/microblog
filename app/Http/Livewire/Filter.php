<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;

class Filter extends Component
{
	use WithPagination;

	public $searchTerm;

    public function render()
    {
    	$query = '%'.$this->searchTerm.'%';

    	return view('livewire.filter', [
    		'users'	=> User::where(function($sub_query){
    			$sub_query->Where('name', 'like', '%'.$this->searchTerm.'%')
    				->orWhere('username', 'like', '%'.$this->searchTerm.'%');
                    
    			})->paginate(10)
    	]);
    }
}