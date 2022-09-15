<div>
    <div class="container">
	    <div class="row">
	        <div class="col-md-12">	            
	            <input type="text"  class="form-control" placeholder="Search" wire:model="searchTerm" />
	            <table class="table table-bordered" style="margin: 10px 0 10px 0;">
	                <tr>
	                    <th>Name</th>
                        <th>Username</th>
	                </tr>
	                @foreach($users as $user)
	                <tr>
	                    <td>
	                        {{ $user->name }}
	                    </td>
	                    <td>
	                        {{ $user->username }}
	                    </td>
                        <td>
	                        <a href="{{ $user->username }}" style="color: blue">View Profile</a>
	                    </td>
	                </tr>
	                @endforeach
	            </table>
	            {{ $users->links() }}
	        </div>
	    </div>
	</div>
</div>