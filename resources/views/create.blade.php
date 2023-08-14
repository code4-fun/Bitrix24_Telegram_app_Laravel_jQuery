@extends('main')

@section('title', 'Bitrix24 app :: Create new contact')

@section('content')

  <form class="form_contact" autoComplete="off">
    <div class="form_title">Create lead</div>
    <div class="input_container">
      <input name="name" type="text" placeholder="Name (FIO) *" />
    </div>
    <div class="input_container">
      <input name="birth" type="datetime-local" placeholder="Birth" />
    </div>
    <div class="input_container">
      <input name="phone" type="text" placeholder="Phone *" />
    </div>
    <div class="input_container">
      <input name="email" type="text" placeholder="Email" />
    </div>
    <div class="input_container">
      <textarea name="comment" type="text" placeholder="Comment"></textarea>
    </div>
    <div class="button_container">
      <input id="create_contact" type="submit" value="Create" />
    </div>
  </form>

@endsection
