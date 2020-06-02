# frozen_string_literal: true

require 'sassc'
require 'autoprefixer-rails'
require 'uglifier'
require 'oj'
require 'rubocop/rake_task'
require 'logger'

$stdout.sync = $stderr.sync = true

# Logger
logger = Logger.new($stdout)
logger.datetime_format = '%F %T %:z'
logger.formatter = proc do |severity, datetime, _progname, msg|
  "#{datetime} | #{severity} | #{msg}\n"
end

# Tests
RuboCop::RakeTask.new

# Helper
class Helper
  attr_reader :ext

  def initialize
    json = Oj.load_file('composer.json')
    @ext = json['name'].split('/')
  end

  def base_dir
    File.join('build', 'package', @ext.first, @ext.last)
  end

  def twig_namespace(file_path)
    file = File.basename(file_path)
    path = asset_path(file_path)

    format(
      '@%<vendor>s_%<extname>s/%<path>s/%<filename>s',
      vendor: @ext.first,
      extname: @ext.last,
      path: path,
      filename: file
    )
  end

  def asset_path(file_path)
    File.extname(file_path).gsub('.', '')
  end

  def minified_ext(file_path)
    ext = File.extname(file_path)

    return file_path if file_path.end_with?('.min' + ext)

    file_path.gsub(ext, '.min' + ext)
  end
end

namespace :build do
  # Input files
  files = {
    css: Dir.glob('styles/**/theme/css/*.css') + Dir.glob('adm/style/css/*.css'),
    js: Dir.glob('styles/**/theme/js/*.js') + Dir.glob('adm/style/js/*.js')
  }

  # Exclude minified files
  files[:css].delete_if { |file| file.end_with?('.min.css') }
  files[:js].delete_if { |file| file.end_with?('.min.js') }

  helper = Helper.new

  # AutoPrefix base task
  task :autoprefix_base, [:opts] do |_t, args|
    args[:opts][:output] = args[:opts][:input] unless args[:opts].key?(:output)
    args[:opts][:output] += '.tmp' if args[:opts][:output].eql?(args[:opts][:input])

    logger.info(format('Processing file: %<filename>s', filename: args[:opts][:input]))
    File.open(args[:opts][:output], 'w') do |f|
      css = File.read(args[:opts][:input])

      f.puts AutoprefixerRails.process(
        css,
        map: false,
        cascade: false,
        from: args[:opts][:input],
        to: args[:opts][:output],
        browsers: [
          '>= 1%',
          'last 1 major version',
          'not dead',
          'Chrome >= 45',
          'Firefox >= 38',
          'Edge >= 12',
          'Explorer >= 10',
          'iOS >= 9',
          'Safari >= 9',
          'Android >= 4.4',
          'Opera >= 30'
        ]
      ).css
    end

    if args[:opts][:output].index(/\.tmp$/).to_i.positive?
      logger.warn(format('Overwriting file: %<filename>s', filename: args[:opts][:input]))
      File.delete(args[:opts][:input])
      File.rename(args[:opts][:output], args[:opts][:input])
    end
  end

  desc 'AutoPrefix CSS files'
  task :autoprefix do
    logger.info('Autoprefixing CSS files')
    files[:css].each do |file|
      Rake::Task['build:autoprefix_base'].reenable
      Rake::Task['build:autoprefix_base'].invoke(
        input: file,
        style: :expanded
      )
    end
  end

  # Generate minified assets
  task :minify_base, [:opts] do |_t, args|
    args[:opts][:output] = helper.minified_ext(args[:opts][:input]) unless args[:opts].key?(:output)
    args[:opts][:path] = helper.asset_path(args[:opts][:input]) unless args[:opts].key?(:path)

    # Minify file
    logger.info(format('Processing file: %<filename>s', filename: args[:opts][:output]))
    case args[:opts][:path]
    when 'css'
      File.open(args[:opts][:output], 'w') do |f|
        css = File.read(args[:opts][:input])

        f.puts SassC::Engine.new(
          css,
          style: :compressed,
          cache: false,
          syntax: :css,
          filename: args[:opts][:output],
          sourcemap: :none
        ).render
      end
    when 'js'
      File.open(args[:opts][:output], 'w') do |f|
        js = File.read(args[:opts][:input])

        f.puts Uglifier.compile(js, harmony: true)
      end
    else
      logger.error(format('Invalid path: %<directory>s', directory: args[:opts][:path]))
      abort
    end
  end

  desc 'Minify assets'
  task :minify do
    logger.info('Minifying assets')
    base_dir = helper.base_dir

    unless Dir.exist?(base_dir)
      logger.fatal(format('Directory not found: %<directory>s', directory: base_dir))
      abort
    end

    Dir.chdir(base_dir) do
      template = Dir.glob('styles/**/template/**/*.html') + Dir.glob('adm/style/**/*.html')

      template.each do |file|
        html = old_html = File.read(file)

        # JS
        files[:js].each do |f|
          namespace = helper.twig_namespace(f)
          next unless html.include?(namespace)

          # Generate minified file
          Rake::Task['build:minify_base'].reenable
          Rake::Task['build:minify_base'].invoke(input: f)

          # Replace filename in template
          html = html.gsub(namespace, helper.minified_ext(namespace))
        end

        # CSS
        files[:css].each do |f|
          namespace = helper.twig_namespace(f)
          next unless html.include?(namespace)

          # Generate minified file
          Rake::Task['build:minify_base'].reenable
          Rake::Task['build:minify_base'].invoke(input: f)

          # Replace filename in template
          html = html.gsub(namespace, helper.minified_ext(namespace))
        end

        next if html.eql?(old_html)

        # Update template file
        logger.warn(format('Overwritting file: %<filename>s', filename: file))
        File.open(file, 'w') { |f| f.puts html }

        unless File.size(file).positive?
          logger.fatal(format('Generated empty file: %<filename>s', filename: file))
          abort
        end
      end
    end
  end

  desc 'Build assets'
  task :all do
    Rake::Task['build:autoprefix'].invoke
    Rake::Task['build:minify'].invoke
  end
end
