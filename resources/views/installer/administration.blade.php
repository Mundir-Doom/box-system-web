<div role="tabpanel" class="tab-pane " id="administration-tab">
    <h3 class="title">Administration</h3>
    <div class="section ">
        <p>Please enter your account details for administration.</p>
        <hr />
        <div class="row">
            <div class="col-md-6">
                <div class="form-group mt-2">
                    <label for="first_name">First Name</label>
                    <input type="text" value="{{ old('first_name') ?? '' }}" id="first_name" name="first_name"
                        class="form-control form--control" placeholder="Your first name" />
                </div>
            </div>
            <div class=" col-md-6">
                <div class="form-group  mt-2">
                    <label for="last_name">Last Name</label>
                    <input type="text" value="{{ old('last_name') ?? '' }}" id="last_name" name="last_name"
                        class="form-control  form--control" placeholder="Your last name" />
                </div>
            </div>
            <div class=" col-md-6">
                <div class="form-group  mt-2">
                    <label for="email">Email</label>
                    <input type="text" value="{{ old('email') ?? '' }}" name="email"
                        class="form-control  form--control" placeholder="Your email" />
                </div>
            </div>
            <div class=" col-md-6">
                <div class="form-group  mt-2">
                    <label for="password">Login Password</label>
                    <div class="input-group">
                        <input type="password" value="{{ old('password') ?? '' }}" id="admin_password" name="password"
                            class="form-control  form--control" placeholder="Login password" />
                        <div class="input-group-append">
                            <button type="button" id="gen-pass" class="btn btn-outline-secondary">Generate</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class=" col-md-12">
                <div class="form-group  mt-2">
                    <label for="password">Purchase code</label>
                    <input type="text" value="{{ old('purchase_code') ?? '' }}" name="purchase_code"
                        class="form-control  form--control" placeholder="Enter your purchase code" />
                </div>
            </div>

            <div class="col-12 mt-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="1" id="do_storage_link" name="do_storage_link" checked>
                    <label class="form-check-label" for="do_storage_link">
                        Create storage symlink after install
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="1" id="do_cache_config" name="do_cache_config" checked>
                    <label class="form-check-label" for="do_cache_config">
                        Cache config and routes after install
                    </label>
                </div>
            </div>

        </div>
    </div>
    <div class="d-flex justify-content-between mt-5">
        <button class="btn btn-primary previous" type="button"><i class='fa fa-arrow-left'></i> Preview</button>
        <button class="btn btn-primary " type="submit"> Finish</button>
    </div>
</div>
