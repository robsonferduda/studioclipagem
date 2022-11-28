from selenium import webdriver
from selenium.webdriver.chrome.options import Options
from webdriver_manager.chrome import ChromeDriverManager

options = Options()
options.headless = True

driver = webdriver.Chrome(ChromeDriverManager().install(), options=options)
driver.get('https://www.urlbox.io')
driver.save_screenshot('screenshot.png')
driver.quit()