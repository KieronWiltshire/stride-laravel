<?php

namespace Domain\Menu;

use Domain\Menu\Events\MenuCreatedEvent;
use Domain\Menu\Events\MenuUpdatedEvent;
use Domain\Menu\Exceptions\CannotCreateMenuException;
use Domain\Menu\Exceptions\CannotUpdateMenuException;
use Domain\Menu\Exceptions\MenuNotFoundException;
use Domain\Menu\Menu;
use Domain\Menu\Validators\MenuCreateValidator;
use Domain\Menu\Validators\MenuUpdateValidator;
use Domain\User\User;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Support\Repositories\AppRepository;
use Domain\Menu\Contracts\Repositories\MenuRepository as MenuRepositoryInterface;

class UserRepository extends AppRepository implements MenuRepositoryInterface
{
  /**
   * @var MenuCreateValidator
   */
  protected $menuCreateValidator;

  /**
   * @var MenuUpdateValidator
   */
  protected $menuUpdateValidator;

  /**
   * Create a new user repository instance.
   *
   * @param MenuCreateValidator $menuCreateValidator
   * @param MenuUpdateValidator $menuUpdateValidator
   */
  public function __construct(
    MenuCreateValidator $menuCreateValidator,
    MenuUpdateValidator $menuUpdateValidator
  ) {
    $this->menuCreateValidator = $menuCreateValidator;
    $this->menuUpdateValidator = $menuUpdateValidator;
  }

  /**
   * Retrieve all of the menus.
   *
   * @return Collection<\Domain\Menu\Menu>
   */
  function all()
  {
    return $this->execute(Menu::query());
  }

  /**
   * Create a new menu.
   *
   * @param array $attributes
   * @return \Domain\Menu\Menu
   *
   * @throws CannotCreateMenuException
   */
  function create($attributes)
  {
    $this->menuCreateValidator->validate($attributes);

    if ($user = Menu::create($attributes)) {
      event(new MenuCreatedEvent($user));

      return $user;
    }

    throw new Exception();
  }

  /**
   * Create a menu if the specified search parameters could not find one
   * with the matching criteria.
   *
   * @param number|string $parameter
   * @param number|string $search
   * @param boolean $regex
   * @param array $attributes
   * @return \Domain\Menu\Menu
   *
   * @throws CannotCreateMenuException
   */
  function firstOrCreate($parameter, $search, $regex = true, $attributes = [])
  {
    $query = Menu::query();

    if ($regex) {
      $query->where($parameter, 'REGEXP', $search);
    } else {
      $query->where($parameter, $search);
    }

    $menu = $this->execute($query, true);

    return ($menu) ? $menu : $this->create($attributes);
  }

  /**
   * Find a menu by an unknown parameter.
   *
   * @param number|string $parameter
   * @param number|string|array $search
   * @param boolean $regex
   * @return Collection<\Domain\Menu\Menu>
   */
  function find($parameter, $search, $regex = true)
  {
    $query = Menu::query();

    if (is_array($parameter)) {
      $query->whereIn($parameter, $search);
    } else {
      if ($regex) {
        $query->where($parameter, 'REGEXP', $search);
      } else {
        $query->where($parameter, $search);
      }
    }

    return $this->execute($query);
  }

  /**
   * Find a user by identifier.
   *
   * @param string $id
   * @return \Domain\Menu\Menu
   *
   * @throws MenuNotFoundException
   */
  function findById($id)
  {
    $menu = $this->execute(Menu::where('id', $id), true);

    if (!$menu) {
      throw new MenuNotFoundException();
    }

    return $menu;
  }

  /**
   * Update a menu.
   *
   * @param \Domain\Menu\Menu $menu
   * @param array $attributes
   * @return \Domain\Menu\Menu
   *
   * @throws CannotUpdateMenuException
   */
  function update(Menu $menu, $attributes)
  {
    $this->menuUpdateValidator->validate($attributes);

    foreach ($attributes as $attr => $value) {
      $menu->$attr = $value;
    }

    if ($menu->save()) {
      event(new MenuUpdatedEvent($menu, $attributes));

      return $menu;
    }
  }

  /**
   * Retrieve all of the menus for the specified user.
   *
   * @param User $user
   * @return Collection<\Domain\Menu\Menu>
   */
  function getMenusForUser(User $user)
  {
    // TODO: Implement getMenusForUser() method.
    return $this->execute(Menu::where('user_id', $user->id), true);
  }
}
