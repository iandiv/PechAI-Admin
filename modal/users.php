<!--ADD Modal -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered ">
        <form action="" method="post">
            <div class="modal-content round  round_sm border ">
                <div class="modal-header  border-0">
                    <h1 class="modal-title fs-5 w-100" id="exampleModalLabel">Add</h1>
                    <button class="btn rounded-circle" type="button" data-bs-dismiss="modal"><i
                            class="bi bi-x-lg"></i></button>


                </div>
                <div class="modal-body ps-sm-5 pe-sm-5">


                    <div class="row mb-2">
                        <div class="col-sm-12">


                            <input type="text" class="round_sm bg-light form-control mb-2" name="username"
                                placeholder="Username">

                        </div>
                        <div class="col-sm-12">

                            <input type="text" class="round_sm bg-light form-control mb-2" name="password"
                                placeholder="passowrd">

                        </div>
                        
                    </div>



                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn  fw-bold text-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn  fw-bold text-primary" name="addUser">Add</button>
                </div>
            </div>
        </form>

    </div>
</div>
<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered ">
        <form action="" method="post">
            <div class="modal-content round  round_sm border ">

                <div class="modal-header  border-0">
                    <h1 class="modal-title fs-5 w-100" id="exampleModalLabel">Edit</h1>
                    <button class="btn rounded-circle" type="button" data-bs-dismiss="modal"><i
                            class="bi bi-x-lg"></i></button>


                </div>

                <div class="modal-body ps-sm-5 pe-sm-5">
                    <form action="" method="post">
                        <div class="row mb-2">
                            <input type="text" class="round_sm bg-light form-control mb-2" name="edtId"
                                placeholder="Edit ID" id="editID" hidden>

                            <div class="col-sm-12">


                                <input type="text" class="round_sm bg-light form-control mb-2" name="edtUsername"
                                    placeholder="Brand" id="editBrand">

                            </div>
                            <div class="col-sm-12">

                                <input type="text" class="round_sm bg-light form-control mb-2" name="edtPassword"
                                    placeholder="Name" id="editName">

                            </div>
                        </div>

                        
                    </form>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn  fw-bold text-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn  fw-bold text-primary" name="editUser">Save</button>
                </div>
            </div>
        </form>
    </div>

</div>





