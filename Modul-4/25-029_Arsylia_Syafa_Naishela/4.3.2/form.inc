
<form name="myForm" action="processData.php" method="POST">
    <fieldset>
        <legend>Person Details</legend>

        <div class="field">
            <label for="surname">Surname</label>
            <input type="text" name="surname" id="surname" size="31"
                value="<?php echo htmlspecialchars($_POST['surname'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"/>
        </div>

        <div class="field">
            <label for="email">Email address</label>
            <input type="text" name="email" id="email" size="31"
                value="<?php echo htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"/>
        </div>

        <div class="field">
            <label for="passwd">Password</label>
            <input type="password" name="passwd" id="passwd" size="31"/>
        </div>

        <div class="field">
            <label for="address">Street Address</label>
            <textarea name="address" id="address" rows="3" cols="22"><?php
                echo htmlspecialchars($_POST['address'] ?? '', ENT_QUOTES, 'UTF-8');
            ?></textarea>
        </div>

        <div class="field">
            <label for="state">State</label>
            <select name="state" id="state">
                <option value="-1" <?php echo (($_POST['state'] ?? '-1') == '-1') ? 'selected' : ''; ?>>[choose your state]</option>
                <option value="1" <?php echo (($_POST['state'] ?? '') == '1') ? 'selected' : ''; ?>>Australian Capital Territory</option>
                <option value="2" <?php echo (($_POST['state'] ?? '') == '2') ? 'selected' : ''; ?>>Queensland</option>
                <option value="3" <?php echo (($_POST['state'] ?? '') == '3') ? 'selected' : ''; ?>>Victoria</option>
                <option value="4" <?php echo (($_POST['state'] ?? '') == '4') ? 'selected' : ''; ?>>New South Wales</option>
                <option value="5" <?php echo (($_POST['state'] ?? '') == '5') ? 'selected' : ''; ?>>Tasmania</option>
                <option value="6" <?php echo (($_POST['state'] ?? '') == '6') ? 'selected' : ''; ?>>South Australia</option>
                <option value="7" <?php echo (($_POST['state'] ?? '') == '7') ? 'selected' : ''; ?>>Western Australia</option>
                <option value="8" <?php echo (($_POST['state'] ?? '') == '8') ? 'selected' : ''; ?>>Northern Territory</option>
            </select>
        </div>

        <div class="field">
            <input type="hidden" name="country" value="Australia"/>
        </div>

        <div class="field">
            <label>Gender</label>
            <input type="radio" name="sex" value="male"
                <?php echo (($_POST['sex'] ?? '') == 'male') ? 'checked' : ''; ?>/>Male

            <input type="radio" name="sex" value="female"
                <?php echo (($_POST['sex'] ?? '') == 'female') ? 'checked' : ''; ?>/>Female
        </div>

        <div class="field">
            <label for="vegetarian">Vegetarian?</label>
            <input type="checkbox" name="vegetarian" id="vegetarian" value="yes"
                <?php echo isset($_POST['vegetarian']) ? 'checked' : ''; ?>/>
        </div>

        <div class="field">
            <input type="submit" value="Submit" name="submit"/>
            <input type="reset" value="Reset" name="reset"/>
        </div>
    </fieldset>
</form>
