<div class="wrap">
  <h1>Настройки кастомных мета-тегов</h1>
  <form class="form_one" method="post">
    <h2>Добавить новый мета-тег</h2>
    <label for="tag_name">Название мета-тега:</label>
    <input type="text" name="tag_name" required>
    <label for="tag_content">Содержимое мета-тега:</label>
    <textarea name="tag_content" required></textarea>
    <label for="pages">ID страниц (через запятую, например: 1,2,3):</label>
    <input type="text" name="pages" placeholder="1,2,3">
    <input type="submit" name="submit" value="Добавить мета-тег">
  </form>

  <h2>Существующие мета-теги</h2>
  <table class="widefat">
    <thead>
      <tr>
        <th>ID</th>
        <th>Название</th>
        <th>Содержимое</th>
        <th>Страницы</th>
        <th>Действия</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($meta_tags) && is_array($meta_tags)): ?>
        <?php foreach ($meta_tags as $tag): ?>
          <tr>
            <td><?php echo esc_html($tag->id); ?></td>
            <td><?php echo esc_html($tag->tag_name); ?></td>
            <td><?php echo esc_html($tag->tag_content); ?></td>
            <td><?php echo esc_html($tag->pages); ?></td>
            <td>
              <form method="post" class="delete">
                <input type="hidden" name="tag_id" value="<?php echo esc_html($tag->id); ?>">
                <input type="submit" name="delete" value="Удалить" class="delete">
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="5" class="no-meta-tags">Нет существующих мета-тегов.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>