@extends('layouts.template')
@section('title','Inicio de Sesión | SAGECIM')

<div class="container-fluid">
    <div class="row h-100 align-items-center justify-content-center">
        <div class="col-12 col-sm-8 col-md-6 col-lg-5 col-xl-4">
            <div class="bg-light rounded p-4 p-sm-5 my-4 mx-3">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <a href="#" class="">
                        <h3 class="text-primary">SAGECIM</h3>
                    </a>
                    <h3>Inicia Sesión</h3>
                </div>
                <form method="POST" action="{{ route('iniciar-sesion') }}">
                    @csrf
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="floatingInput" name="name" placeholder="Username">
                    <label for="floatingInput">Usuario</label>
                </div>
                <div class="form-floating mb-4">
                    <input type="password" class="form-control" id="floatingPassword" name="password" placeholder="Password">
                    <label for="floatingPassword">Contraseña</label>
                </div>
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="exampleCheck1">
                        <label class="form-check-label" for="exampleCheck1">Recuérdame</label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary py-3 w-100 mb-4">Iniciar Sesión</button>
                <p class="text-center mb-0">¿No te has registrado? <a href="{{ route('signup') }}">Regístrate</a></p>
                </form>
            </div>
        </div>
    </div>
</div>