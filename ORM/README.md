# ORM
eloquent ORM in PHP

## required

- PHP >= 5.4
- Composer

## install

```bash
git clone https://github.com/natilosir/ORM/
```
<br><br>

## Example

- Select all data with chaining methods
```bash
$users = DB::Table('users')
    ->where('name', 'Jane Doe')
    ->orderBy('id', 'DESC')
    ->limit(3)
    ->get();
    
```

- Search with multiple conditions
```bash
$searchResults = DB::Table('users')
    ->search(['name' => 'Jane', 'email' => 'example.com'])
    ->orderBy('id', 'deSC')
    ->limit(3)
    ->get();

foreach ($searchResults as $result) {
    echo $result['id'].' - '.$result['name'].' - '.$result['email'].'<br>';
}
```

- Count users with where condition
```bash
$userCount = DB::Table('users')
 // ->where('age', 25)  or search
    ->where('age', 25, '>')
    ->where('age', 25, '>', 'OR')
    ->where('age', 25, '=', 'and')
    ->count();
```

- Use the orderBy clause to sort users by email in ascending order with a limit
```bash
$orderedResults = DB::Table('users')
    ->where('name', 'John')
    ->where('name', 'jane')
    ->orderBy('id', 'deSC')
    ->limit(3)
    ->get();

foreach ($orderedResults as $result) {
    echo $result['id'].' - '.$result['name'].' - '.$result['email'].'<br>';
}
```

- Insert new data with array
```bash
$newUser = [
    'name'  => 'Jane Doe',
    'email' => 'jane.doe@example.com'];
    
DB::Table('users')
    ->insert($newUser);
```

- Insert new data with model instance eloquent
```bash
$data        = DB::Table('users');
$data->user  = 'first';
$data->name  = 'second';
$data->email = 'third';
$data->save();
```

- Update data with array
```bash
$updateData = [
    'name'  => 'John Smith',
    'email' => 'john.smith@example.com'];
    
DB::Table('users')
    ->update(1, ['name' => $updateDataSingle]); // 1 is id and where is with array ->update(['name' => 'second'], ['name' => $updateDataSingle]);
```

- Update data with model instance eloquent
```bash
$data        = DB::Table('users');
$data->user  = 'first';
$data->name  = 'second';
$data->email = 'third';
$data->save(1); // 1 is id and where is with array $data->save('name' => 'Jane Doe'); 
```

- Delete data
```bash
DB::Table('users')
    ->delete(1); // 1 is id and where is with array ->delete(['name' => 'Jane Doe']);
```

- Using DISTINCT in SQL
```bash
$users = DB::Table('users')
    ->select('email')
    ->distinct()
    ->get();

foreach ($users as $user) {
    echo $user['email'].'<br>';
}
```

- Run a custom SQL query
```bash
$customQueryResults = DB::Table('users')
    ->query("SELECT * FROM users WHERE email LIKE '%example.com%' LIMIT 5");

foreach ($customQueryResults as $result) {
    echo $result['id'].' - '.$result['name'].' - '.$result['email'].'<br>';
}
```
