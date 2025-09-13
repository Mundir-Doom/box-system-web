<div role="tabpanel" class="tab-pane wow zoomIn" id="database-configuration-tab">
    <h3 class="title">Database & App Configuration</h3>
    <div class="section clearfix">
        <p>Please enter your database connection details and app URL. You can test the DB connection before continuing.</p>
        <hr />
        <div class="row">
            <div class="col-md-6">
            <div class="form-group mt-2 ">
                <label for="host" >Database Host</label>
                    <input type="text" value="{{old('host') ?? 'localhost'}}" id="host"  name="host" class="form-control  form--control" placeholder="Database Host (usually localhost)"  />
                </div>
            </div>
            <div class=" col-md-6">
            <div class="form-group  mt-2">
                <label for="dbport" >Database Port</label>
                    <input type="text" value="{{old('dbport') ?? '3306'}}" id="dbport" name="dbport" class="form-control  form--control" placeholder="3306" />
                </div>
            </div>
            <div class=" col-md-6">
            <div class="form-group  mt-2">
                <label for="dbuser" >Database User</label>
                    <input type="text" value="{{old('dbuser') ?? ''}}" name="dbuser" class="form-control  form--control" autocomplete="off" placeholder="Database user name" />
                </div>
            </div>
            <div class=" col-md-6">
            <div class="form-group  mt-2">
                <label for="dbpassword" >Password</label>
                    <input type="password" value="{{old('dbpassword') ?? ''}}" name="dbpassword" class="form-control  form--control" autocomplete="off" placeholder="Database user password" />
                </div>
            </div>
            <div class=" col-md-6">
            <div class="form-group  mt-2">
                <label for="dbname" >Database Name</label>
                    <input type="text" value="{{old('dbname') ?? ''}}" name="dbname" class="form-control  form--control" placeholder="Database Name" />
                </div>
            </div>
            <div class=" col-md-6">
            <div class="form-group  mt-2">
                <label for="app_url" >App URL</label>
                    <input type="text" value="{{ old('app_url') ?? (request()->getSchemeAndHttpHost() ?? '') }}" id="app_url" name="app_url" class="form-control  form--control" placeholder="https://your-domain.com" />
                </div>
            </div>
            <div class="col-12">
                <button type="button" id="btn-test-db" class="btn btn-outline-primary mt-2">Test Database Connection</button>
                <span id="db-test-result" class="ml-2"></span>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-between mt-5">
        <button   class="btn btn-primary previous"  type="button"><i class='fa fa-arrow-left'></i> Preview</button>
        <button   class="btn btn-primary form-next" type="button">  Next  <i class='fa fa-arrow-right'></i></button>
    </div>
</div>
