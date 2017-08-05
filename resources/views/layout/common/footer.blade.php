<footer class="castle-footer">
	<div class="container">
		<div class="row">
			<div class="col-sm-8 castle-footer-left">
				<!-- some kind of disclaimer or footer text might go here -->
			</div>
			<div class="col-sm-4 castle-footer-right">
				<p>Castle {{ app('version')->tag ?: 'dev ('.app('version')->branch.')' }} <samp>{{ app('version')->revision }}{{ app('version')->dirty ? '-dirty' : '' }}</samp></p>
			</div>
		</div>
	</div>
</footer>
