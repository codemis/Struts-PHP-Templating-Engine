if ARGV[1].nil? 
  @speak = false
else
  @speak = true
end
# A watchr script for running the tests automatically
watch('tests/(.*).class.test.php')  { |m| run_single_test(m[0]) }
watch('(.*).class.php')  { |m| run_all_tests }

def run_single_test(file) 
  system("clear") 
  send_response("Running Test: #{File.basename(file)}")
  run("cd tests && phpunit #{File.basename(file)}", File.basename(file))
end 

def run_all_tests
  system("clear") 
  send_response("Running All Tests")
  Dir.glob('tests/*.class.test.php') do |test_file|
    send_response("Running Test: #{File.basename(test_file)}")
    run("cd tests && phpunit #{File.basename(test_file)}", File.basename(test_file))
  end 
end

def run(cmd, file)  
  puts(cmd) 
  result = `#{cmd}` 
  growl(result, file) rescue nil 
  puts result 
end 

def growl(message, file)
  if message=~ /FAILURES/ || message =~ /undefined method/
    message = "Test Failed in #{file}!"
    image = "~/Pictures/GrowlNotification/fail.png"
  else
    message = "Test Passed!"
    image = "~/Pictures/GrowlNotification/pass.png"
  end
  send_response("!------[#{message}]-----!")
  growlnotify = `which growlnotify`.chomp 
  options = "-w -n Watchr --image '#{File.expand_path(image)}' -m '#{message}'" 
  system %(#{growlnotify} #{options} &)
end

def send_response(message)
  if @speak === true
    system("say #{message.gsub(/[^0-9a-z ]/i, '')}")
    sleep 1
  else
    puts message
  end
end