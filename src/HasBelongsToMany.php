<?php

namespace OsTheNeo\NovaFields;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasBelongsToMany {
	public function model($relationModel): BelongsToMany{
		$model = app($relationModel);
		return $this->belongsToMany($model);
	}
	
	public function syncManyValues($values, $attribute, $relationModel){
		$arrayIds = array_column($values, 'id');
		$this->$attribute()->sync($arrayIds);
	}
	
}
