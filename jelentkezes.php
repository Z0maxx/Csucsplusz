<?php include "header.php" ?>
<title>Jelentkezés | Csúcsplusz Autósiskola</title>
<main class="bg-sky-200">
    <section class="flex flex-col items-center gap-4 py-4 m-auto w-9/10 sm:w-auto">
        <h1 class="text-xl md:text-2xl font-medium text-center text-border-light text-indigo-600 [--text-border-color:var(--color-teal-300)]">Az alábbi adatlapot kitöltve tud jelentkezni tanfolyamunkra.</h1>
        <form class="flex flex-col w-full gap-4 p-2 border-2 md:p-4 sm:w-fit border-sky-300 bg-cyan-100 rounded-xl">
            <div class="flex flex-col gap-2 px-2 pt-1 pb-2.5 rounded-md border-amber-500 border-2 border-green-500 bg-slate-200">
                <label for="your-name" class="text-lg font-medium md:text-xl text-mauve-600">Név <span class="text-xl font-bold text-red-500 md:text-2xl leading-1">*</span></label>
                <input type="text" id="your-name" name="your-name" autocomplete="name" placeholder=" " class="py-0.5 px-1 bg-blue-100 rounded-sm focus:outline-cyan-500 outline-2 outline-blue-300 caret-sky-800">
            </div>
            <div class="flex flex-col gap-2 px-2 pt-1 pb-2.5 rounded-md has-[input:invalid]:border-amber-500 border-2 border-green-500 bg-slate-200">
                <label for="birth-name" class="text-lg font-medium md:text-xl text-mauve-600">Születéskori név <span class="text-xl font-bold text-red-500 md:text-2xl leading-1">*</span></label>
                <input type="text" id="birth-name" name="birth-name" class="py-0.5 px-1 bg-blue-100 rounded-sm focus:outline-cyan-500 outline-2 outline-blue-300 caret-sky-800">
            </div>
            <div class="flex flex-col gap-2 px-2 pt-1 pb-2.5 rounded-md has-[input:invalid]:border-amber-500 border-2 border-green-500 bg-slate-200">
                <label for="birth-place" class="text-lg font-medium md:text-xl text-mauve-600">Születési hely <span class="text-xl font-bold text-red-500 md:text-2xl leading-1">*</span></label>
                <input type="text" id="birth-place" name="birth-place" class="py-0.5 px-1 bg-blue-100 rounded-sm focus:outline-cyan-500 outline-2 outline-blue-300 caret-sky-800">
            </div>
            <div class="flex flex-col gap-2 px-2 pt-1 pb-2.5 rounded-md has-[input:invalid]:border-amber-500 border-2 border-green-500 bg-slate-200">
                <label for="birth-date" class="text-lg font-medium md:text-xl text-mauve-600">Születési idő <span class="text-xl font-bold text-red-500 md:text-2xl leading-1">*</span></label>
                <input type="date" id="birth-date" name="birth-date" class="py-0.5 px-1 bg-blue-100 rounded-sm focus:outline-cyan-500 outline-2 outline-blue-300 caret-sky-800">
            </div>
            <div class="flex flex-col gap-2 px-2 pt-1 pb-2.5 rounded-md has-[input:invalid]:border-amber-500 border-2 border-green-500 bg-slate-200">
                <label for="mother-name" class="text-lg font-medium md:text-xl text-mauve-600">Anyja születési neve <span class="text-xl font-bold text-red-500 md:text-2xl leading-1">*</span></label>
                <input type="text" id="mother-name" name="mother-name" class="py-0.5 px-1 bg-blue-100 rounded-sm focus:outline-cyan-500 outline-2 outline-blue-300 caret-sky-800">
            </div>
            <div class="flex flex-col gap-2 px-2 pt-1 pb-2.5 rounded-md :border-amber-500 border-2 border-green-500 bg-slate-200">
                <label for="citizenship" class="text-lg font-medium md:text-xl text-mauve-600">Állampolgárság <span class="text-xl font-bold text-red-500 md:text-2xl leading-1">*</span></label>
                <input type="text" id="citizenship" name="citizenship" value="magyar" class="py-0.5 px-1 py-0.5 bg-blue-100 rounded-sm focus:outline-cyan-500 outline-2 outline-blue-300 caret-sky-800">
            </div>
            <div class="flex flex-col gap-2 px-2 pt-1 pb-2.5 rounded-md has-[select:has(option[value]:checked)]:border-amber-500 border-2 border-green-500 bg-slate-200">
                <label for="school" class="text-lg font-medium md:text-xl text-mauve-600">Legmagasabb iskolai végzettség <span class="text-xl font-bold text-red-500 md:text-2xl leading-1">*</span></label>
                <select id="school" name="school" class="py-0.5 px-1 bg-blue-100 rounded-sm cursor-pointer focus:outline-cyan-500 outline-2 outline-blue-300 caret-sky-800">
                    <option value="" hidden disabled selected></option>
                    <option value="Legfeljebb 8 általános">Legfeljebb 8 általános</option>
                    <option value="Szakiskola, szakmunkásképzpő">Szakiskola, szakmunkásképző</option>
                    <option value="Gimnázium">Gimnázium</option>
                    <option value="Szakközépiskola">Szakközépiskola</option>
                    <option value="Főiskola, egyetem, PhD">Főiskola, egyetem, PhD</option>
                </select>
            </div>
            <div class="flex flex-col gap-2 px-2 pt-1 pb-2.5 rounded-md has-[input:invalid]:border-amber-500 border-2 border-green-500 bg-slate-200">
                <label for="id-number" class="text-lg font-medium md:text-xl text-mauve-600">Személyi igazolvány szám <span class="text-xl font-bold text-red-500 md:text-2xl leading-1">*</span></label>
                <input type="text" id="id-number" name="id-number" class="py-0.5 px-1 bg-blue-100 rounded-sm focus:outline-cyan-500 outline-2 outline-blue-300 caret-sky-800">
            </div>
            <div class="flex flex-col gap-2 px-2 pt-1 pb-2.5 rounded-md has-[input:invalid]:border-amber-500 border-2 border-green-500 bg-slate-200">
                <label for="home-address" class="text-lg font-medium md:text-xl text-mauve-600">Tartózkodási cím <span class="text-xl font-bold text-red-500 md:text-2xl leading-1">*</span></label>
                <input type="text" id="home-address" name="home-address" autocomplete="off" class="py-0.5 px-1 bg-blue-100 rounded-sm focus:outline-cyan-500 outline-2 outline-blue-300 caret-sky-800">
            </div>
            <div class="flex flex-col gap-2 px-2 pt-1 pb-2.5 rounded-md has-[input:invalid]:border-amber-500 border-2 border-green-500 bg-slate-200">
                <label for="mail-address" class="text-lg font-medium md:text-xl text-mauve-600">Értesítési cím <span class="text-xl font-bold text-red-500 md:text-2xl leading-1">*</span></label>
                <input type="text" id="mail-address" name="mail-address" autocomplete="off" class="py-0.5 px-1 bg-blue-100 rounded-sm focus:outline-cyan-500 outline-2 outline-blue-300 caret-sky-800">
            </div>
            <div class="flex flex-col gap-2 px-2 pt-1 pb-2.5 rounded-md has-[input:invalid]:border-amber-500 border-2 border-green-500 bg-slate-200">
                <label for="phone" class="text-lg font-medium md:text-xl text-mauve-600">Telefon <span class="text-xl font-bold text-red-500 md:text-2xl leading-1">*</span></label>
                <input type="text" id="phone" name="phone" autocomplete="phone" class="py-0.5 px-1 bg-blue-100 rounded-sm focus:outline-cyan-500 outline-2 outline-blue-300 caret-sky-800">
            </div>
            <div class="flex flex-col gap-2 px-2 pt-1 pb-2.5 rounded-md has-[input:invalid]:border-amber-500 border-2 border-green-500 bg-slate-200">
                <label for="taxnum" class="text-lg font-medium md:text-xl text-mauve-600">Adóazonosító jel <span class="text-xl font-bold text-red-500 md:text-2xl leading-1">*</span></label>
                <input type="text" id="taxnum" name="taxnum" class="py-0.5 px-1 bg-blue-100 rounded-sm focus:outline-cyan-500 outline-2 outline-blue-300 caret-sky-800">
            </div>
            <div class="flex flex-col gap-2 px-2 pt-1 pb-2.5 rounded-md has-[input:invalid]:border-amber-500 border-2 border-green-500 bg-slate-200">
                <label for="email" class="text-lg font-medium md:text-xl text-mauve-600">Email cím <span class="text-xl font-bold text-red-500 md:text-2xl leading-1">*</span></label>
                <input type="email" id="email" name="email" autocomplete="email" class="py-0.5 px-1 bg-blue-100 rounded-sm focus:outline-cyan-500 outline-2 outline-blue-300 caret-sky-800">
            </div>
            <div class="flex flex-col gap-1.5 px-2 pt-1 pb-2.5 rounded-md has-[input:invalid]:border-amber-500 border-2 border-green-500 bg-slate-200">
                <span class="text-lg font-medium md:text-xl text-mauve-600">Járművezetéstől el vagyok tiltva <span class="text-xl font-bold text-red-500 md:text-2xl leading-1">*</span></span>
                <div class="flex gap-4">
                    <label for="drive-banned-yes" class="cursor-pointer has-[input:focus]:outline-cyan-500 outline-2 outline-transparent rounded-sm px-1">
                        <input type="radio" id="drive-banned-yes" name="drive-banned" value="Igen" class="py-0.5 px-1 bg-blue-100 border-2 border-transparent focus:border-cyan-500 caret-sky-800">
                        <span>Igen</span>
                    </label>
                    <label for="drive-banned-no" class="cursor-pointer has-[input:focus]:outline-cyan-500 outline-2 outline-transparent rounded-sm px-1">
                        <input type="radio" id="drive-banned-no" name="drive-banned" value="Nem" checked class="py-0.5 px-1 bg-blue-100 caret-sky-800">
                        <span>Nem</span>
                    </label>
                </div>
            </div>
            <div class="flex flex-col gap-1.5 px-2 pt-1 pb-2.5 rounded-md has-[input:invalid]:border-amber-500 border-2 border-green-500 bg-slate-200">
                <span class="text-lg font-medium md:text-xl text-mauve-600">2 éven belüli forgalmi vizsga <span class="text-xl font-bold text-red-500 md:text-2xl leading-1">*</span></span>
                <div class="flex gap-4">
                    <label for="have-traffic-exam-yes" class="cursor-pointer has-[input:focus]:outline-cyan-500 outline-2 outline-transparent rounded-sm px-1">
                        <input type="radio" id="have-traffic-exam-yes" name="have-traffic-exam" value="Van" class="py-0.5 px-1 bg-blue-100 border-2 border-transparent focus:border-cyan-500 caret-sky-800">
                        <span>Van</span>
                    </label>
                    <label for="have-traffic-exam-no" class="cursor-pointer has-[input:focus]:outline-cyan-500 outline-2 outline-transparent rounded-sm px-1">
                        <input type="radio" id="have-traffic-exam-no" name="have-traffic-exam" value="Nincs" checked class="py-0.5 px-1 bg-blue-100 caret-sky-800">
                        <span>Nincs</span>
                    </label>
                </div>
            </div>
            <div class="flex flex-col gap-1.5 px-2 pt-1 pb-2.5 rounded-md has-[input:invalid]:border-amber-500 border-2 border-green-500 bg-slate-200">
                <span class="text-lg font-medium md:text-xl text-mauve-600">Kamera használatához hozzájárulok a KRESZ vizsgán <span class="text-xl font-bold text-red-500 md:text-2xl leading-1">*</span></span>
                <div class="flex gap-4">
                    <label for="use-camera-yes" class="cursor-pointer has-[input:focus]:outline-cyan-500 outline-2 outline-transparent rounded-sm px-1">
                        <input type="radio" id="use-camera-yes" name="use-camera" value="Igen" checked class="py-0.5 px-1 bg-blue-100 border-2 border-transparent focus:border-cyan-500 caret-sky-800">
                        <span>Igen</span>
                    </label>
                    <label for="use-camera-no" class="cursor-pointer has-[input:focus]:outline-cyan-500 outline-2 outline-transparent rounded-sm px-1">
                        <input type="radio" id="use-camera-no" name="use-camera" value="Nem" class="py-0.5 px-1 bg-blue-100 caret-sky-800">
                        <span>Nem</span>
                    </label>
                </div>
            </div>
            <div class="flex flex-col gap-2 px-2 pt-1 pb-2.5 rounded-md has-[select:invalid]:border-amber-500 border-2 border-green-500 bg-slate-200">
                <label for="courseType" class="text-lg font-medium md:text-xl text-mauve-600">Tanfolyam típusa <span class="text-xl font-bold text-red-500 md:text-2xl leading-1">*</span></label>
                <select id="courseType" name="courseType" class="py-0.5 px-1 bg-blue-100 rounded-sm cursor-pointer focus:outline-cyan-500 outline-2 outline-blue-300 caret-sky-800">
                    <option value="" hidden disabled selected></option>
                    <option value="E-learning">E-learning</option>
                    <option value="Tantermi">Tantermi</option>
                </select>
            </div>
            <div class="flex flex-col gap-2 px-2 pt-1 pb-2.5 rounded-md has-[input:invalid]:border-amber-500 border-2 border-green-500 bg-slate-200">
                <label for="attachedFile" class="text-lg font-medium md:text-xl text-mauve-600">Kérjük csatolja vezetői engedélyét vagy orvosi igazolását! <span class="text-xl font-bold text-red-500 md:text-2xl leading-1">*</span></label>
                <input type="file" id="attachedFile" name="attachedFile" class="py-0.5 px-1 bg-blue-100 rounded-sm cursor-pointer file:bg-sky-300 file:rounded-sm file:my-1 file:px-1 file:py-0.5 focus:outline-cyan-500 outline-2 outline-blue-300 file:font-medium">
            </div>
            <div class="flex flex-col gap-2 px-2 pt-1 pb-2.5 border-2 rounded-md border-slate-500 bg-slate-200">
                <label for="your-message" class="text-lg font-medium md:text-xl text-mauve-600">Üzenet</label>
                <textarea id="your-message" name="your-message" rows="5" class="py-0.5 px-1 bg-blue-100 rounded-sm focus:outline-cyan-500 outline-2 outline-blue-300 caret-sky-800"></textarea>
            </div>
            <div>
                <button type="submit" class="w-full py-2 text-lg md:text-xl font-bold transition border-2 rounded-md shadow-xl cursor-pointer text-cyan-800 bg-emerald-200 border-emerald-400 hover:shadow-green-500/40 focus:shadow-green-500/40 shadow-black-500/40 focus:[&>span]:scale-110 hover:[&>span]:scale-110"><span class="block transition">Küldés</span></button>
            </div>
        </form>
    </section>
</main>
<?php include "footer.php" ?>