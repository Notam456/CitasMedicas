@extends('layouts.template')
@section('title','Registrar | SAGECIM')

<div class="container-fluid">
    <div class="row h-100 align-items-center justify-content-center">
        <div class="col-12 col-sm-8 col-md-6 col-lg-5 col-xl-4">
            <div class="bg-light rounded p-4 p-sm-5 my-4 mx-3">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <a href="#" class="">
                        <h3 class="text-primary">SAGECIM</h3>
                    </a>
                    <h3>Registrar</h3>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="floatingText" placeholder="jhondoe">
                    <label for="floatingText">Username</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="floatingInput" placeholder="name@example.com">
                    <label for="floatingInput">Email</label>
                </div>
                <div class="form-floating mb-4">
                    <input type="password" class="form-control" id="floatingPassword" placeholder="Password">
                    <label for="floatingPassword">Contraseña</label>
                </div>
                <div class="d-flex align-items-center justify-content-between mb-4">
                </div>
                <button type="submit" class="btn btn-primary py-3 w-100 mb-4">Registrarme</button>
                <p class="text-center mb-0">¿Ya tienes cuenta? <a href="#">Inicia Sesión</a></p>
            </div>
        </div>
    </div>
</div>