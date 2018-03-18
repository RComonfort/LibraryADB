<?php
	interface IBook {
		public function get($loanID);
		public function save();
	}
?>